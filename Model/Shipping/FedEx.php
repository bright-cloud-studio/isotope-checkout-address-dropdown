<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */


 
namespace IsotopeFedEx\Model\Shipping;

use IsotopeFedEx\Model\ShippingBox;
use IsotopeFedEx\ShipmentPacker;

use Contao\Database;
use Contao\Database\Result;
use Contao\Environment;
use Contao\Session;
use Contao\StringUtil;

use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Interfaces\IsotopeShipping;
use Isotope\Isotope;
use Isotope\Model\Shipping;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\ProductCollectionSurcharge;
use Isotope\Template;

use FedEx\RateService\Request;
use FedEx\RateService\ComplexType;
use FedEx\RateService\SimpleType;



/**
 * Class Flat
 *
 * @property string flatCalculation
 */
class FedEx extends Shipping implements IsotopeShipping
{

	protected $strServiceCode = false;
	protected $arrRates = array();

	protected $arrShippingExemptStates;

	function __construct(Result $objResult = null)
    {
        parent::__construct($objResult);
		
		$this->arrShippingExemptStates = StringUtil::deserialize($this->avalaraShippingExemptStates);
		
		$session = Session::getInstance();
		
		if ($session->get('fedex_shipping_service') && !$this->strServiceCode) {
			$this->strServiceCode = $session->get('fedex_shipping_service');
		}

		if ($this->fedExAllowedServices && !is_array($this->fedExAllowedServices)) {
			$this->fedExAllowedServices = StringUtil::deserialize($this->fedExAllowedServices);
		}
		foreach($this->fedExAllowedServices as $intIndex => $strCode) {
			if (substr($strCode, 0, 6) != "FEDEX:") {
				$this->fedExAllowedServices[$intIndex] = "FEDEX:" .$strCode;
			}
		}
		
	}

    /**
     * Return information or advanced features in the backend.
     * Use this function to present advanced features or basic shipping information for an order in the backend.
     * @param integer
     * @return string
     */
    public function backendInterface($orderId)
    {
		$objTemplate = new \Isotope\Template('fedex_shipping_details');
		$objTemplate->document_number = $objOrder->document_number;
		
		$objTemplate->buttons = '<div id="tl_buttons"><a href="' . ampersand(str_replace('&key=shipping', '', \Environment::get('request'))) . '" class="header_back" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['backBT']) . '">' . $GLOBALS['TL_LANG']['MSC']['backBT'] . '</a></div>';
		$objTemplate->shipping_method = $GLOBALS['TL_LANG']['MODEL']['tl_iso_shipping.'.$this->type][0];

		return $objTemplate->parse();
    }


   /**
     * Return calculated price for this shipping method
     * @return float
     */
    public function getRates(IsotopeProductCollection $objCollection = null)
    {
		
		if ($this->fedExAllowedServices && !is_array($this->fedExAllowedServices)) {
			$this->fedExAllowedServices = StringUtil::deserialize($this->fedExAllowedServices);
		}
		
		$arrModuleRates = array();
		
		$objConfig = Isotope::getConfig();
		$objShippingAddress = Isotope::getCart()->getShippingAddress();

		if (in_array($objShippingAddress->subdivision, $this->arrShippingExemptStates)) {
			$this->tax_class = 0;
		}
		
		if (stristr($objShippingAddress->subdivision, '-') !== false) {
			list($strCountryCode, $strStateAbbreviation) = explode('-', $objShippingAddress->subdivision, 2); 
		} else {
			$strStateAbbreviation = $objShippingAddress->subdivision;
		}
		
		$objPacker = new ShipmentPacker();
		$objPacker->setBoxSizes($this);
		$arrBoxes = $objPacker->packOrder($objCollection);
		
		$fltShippingCost = 0.00;
		$fltHandlingFee = 0.00;
		
		$objRateRequest = new ComplexType\RateRequest();

		$objRateRequest->WebAuthenticationDetail->UserCredential->Key = $this->fedExApiKey;
		$objRateRequest->WebAuthenticationDetail->UserCredential->Password = $this->fedExApiPassword;
		$objRateRequest->ClientDetail->AccountNumber = $this->fedExAccountNumber;
		$objRateRequest->ClientDetail->MeterNumber = $this->fedExMeterNumber;

		$objRateRequest->TransactionDetail->CustomerTransactionId = $objCollection->id;

		$objRateRequest->Version->ServiceId = 'crs';
		$objRateRequest->Version->Major = 28;
		$objRateRequest->Version->Minor = 0;
		$objRateRequest->Version->Intermediate = 0;

		$objRateRequest->ReturnTransitAndCommit = true;

		$strFromState = $this->shipFromSubdivision;
		if (stristr($strFromState, '-') !== FALSE) {
			list($strCounty, $strFromState) = explode('-', $strFromState);
		}
		$objRateRequest->RequestedShipment->PreferredCurrency = 'USD';
		
		$arrStreetLines = [];
		if ($this->shipFromStreet1) {
			$arrStreetLines[] = $this->shipFromStreet1;
		}
		if ($this->shipFromStreet2) {
			$arrStreetLines[] = $this->shipFromStreet2;
		}
		if ($this->shipFromStreet3) {
			$arrStreetLines[] = $this->shipFromStreet3;
		}
		$objRateRequest->RequestedShipment->Shipper->Address->StreetLines = $arrStreetLines;
		$objRateRequest->RequestedShipment->Shipper->Address->City = $this->shipFromCity;
		$objRateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = strtoupper($strFromState);
		$objRateRequest->RequestedShipment->Shipper->Address->PostalCode = $this->shipFromPostal;
		$objRateRequest->RequestedShipment->Shipper->Address->CountryCode =  strtoupper($this->shipFromCountry);

		$arrStreetLines = [];
		if ($objShippingAddress->street_1) {
			$arrStreetLines[] = $objShippingAddress->street_1;
		}
		if ($objShippingAddress->street_2) {
			$arrStreetLines[] = $objShippingAddress->street_2;
		}
		if ($objShippingAddress->street_3) {
			$arrStreetLines[] = $objShippingAddress->street_3;
		}
		$objRateRequest->RequestedShipment->Recipient->Address->StreetLines = $arrStreetLines;
		$objRateRequest->RequestedShipment->Recipient->Address->City = $objShippingAddress->city;
		$objRateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode =  strtoupper($strStateAbbreviation);
		$objRateRequest->RequestedShipment->Recipient->Address->PostalCode = $objShippingAddress->postal;
		$objRateRequest->RequestedShipment->Recipient->Address->CountryCode =  strtoupper($strCountryCode);

		$objRateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;

		$objRateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_PREFERRED, SimpleType\RateRequestType::_LIST];

		$arrPackages = [];
		
		$fltBoxCharge = 0.00;
		foreach ($arrBoxes as $objShippingBox) {
			$objPackage = new ComplexType\RequestedPackageLineItem();
			$objPackage->Weight->Value = ceil($objShippingBox->getTotalWeight(false));
			$objPackage->Weight->Units = SimpleType\WeightUnits::_LB;
			$objPackage->Dimensions->Length = ShippingBox::scaleLengthDown($objShippingBox->getOuterLength());
			$objPackage->Dimensions->Width = ShippingBox::scaleLengthDown($objShippingBox->getOuterWidth());
			$objPackage->Dimensions->Height = ShippingBox::scaleLengthDown($objShippingBox->getOuterDepth());
			$objPackage->Dimensions->Units = SimpleType\LinearUnits::_IN;
			$objPackage->GroupPackageCount = 1;
			$arrPackages[] = $objPackage;
			$fltBoxCharge += $objShippingBox->boxCharge;
		}

		$objRateRequest->RequestedShipment->PackageCount = count($arrPackages);
		$objRateRequest->RequestedShipment->RequestedPackageLineItems = $arrPackages;

		$objRateServiceRequest = new Request();
		if ($this->fedExMode == 'production') {
			$objRateServiceRequest->getSoapClient()->__setLocation(Request::PRODUCTION_URL);
		} else {
			$objRateServiceRequest->getSoapClient()->__setLocation(Request::TESTING_URL);
		}
		
		$objRateReply = $objRateServiceRequest->getGetRatesReply($objRateRequest); // send true as the 2nd argument to return the SoapClient's stdClass response.


		if (!empty($objRateReply->RateReplyDetails)) {
			foreach ($objRateReply->RateReplyDetails as $objRateReplyDetail) {
				$arrRate = array();
				$strCode = "FEDEX:" .(string)$objRateReplyDetail->ServiceType;
				$strLabel = ucwords(strtolower(str_replace('_', ' ', (string)$objRateReplyDetail->ServiceType)));
				if (!empty($objRateReplyDetail->RatedShipmentDetails)) {
					foreach ($objRateReplyDetail->RatedShipmentDetails as $objRatedShipmentDetail) {

						if ($this->allowedServices && !in_array($strCode, $this->allowedServices)) {
							continue;
						}

						$arrRate['price'] = $objRatedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount + $fltBoxCharge;
						$arrRate['name'] = $strLabel;
					}
				}
				$arrModuleRates[$strCode] = $arrRate;
			}
		}
		
		foreach($arrModuleRates as $strCode => $arrModuleRate) {
			$fltHandling = floatval($this->getHandlingCharge($objCollection, $arrModuleRates[$strCode]['price']));
			$arrModuleRates[$strCode]['price'] += $fltHandling; 
		}
		
		if (empty($arrModuleRates)) {
			$this->blnError = true;	
		}
		
		$this->arrRates = $arrModuleRates;
		
        return $arrModuleRates;
    }
	

    /**
     * Return calculated price for this shipping method
     * @return float
     */
    public function getPrice(IsotopeProductCollection $objCollection = null)
    {
		$fltPrice = false;
		if (!$this->arrRates) {
			$this->getRates($objCollection);
		}
		
		
		if (!$this->strServiceCode) {	
			$fltPrice = false;
			$strService = 'FEDEX_2_DAY';
						
			foreach($this->arrRates as $strCode => $arrRate) {
				if (floatval($arrRate['price']) < $fltPrice || $fltPrice === false && in_array($strCode, $this->fedExAllowedServices)) {
					$fltPrice = floatval($arrRate['price']);
					$strService = $strCode;
				}
			}
			$this->strServiceCode = $strService;
			$fltPrice = floatval($this->arrRates[$this->strServiceCode]['price']);
		} else {
			if (!$fltPrice) {
				$fltPrice = floatval($this->arrRates[$this->strServiceCode]['price']);
			}
		}
		
        return Isotope::calculatePrice($fltPrice, $this, 'price', $this->arrData['tax_class']);
    }
	
    /**
     * @inheritdoc
     */
    public function getLabel()
    {
		if (!$this->arrRates) {
			return "FedEx";
		}
		
		if (!$this->strServiceCode) {	
			return "FedEx";
		} else {
			return $this->arrRates[$this->strServiceCode]['name'];
		}
    }
	

    /**
     * Return calculated price for this shipping method
     * @return float
     */
    public function getHandlingCharge(IsotopeProductCollection $objCollection = null, $fltShipping = 0.00)
    {
		if (!$this->handlingFees) 
		{
			return false;
		}
		
		$fltCharge = 0.00;
		
		$arrFees = StringUtil::deserialize($this->handlingFees);
		
		foreach($arrFees as $arrFee) {
			$fltFee = 0.00;
			if ('%' === substr($arrFee['fee'], -1)) {
				$fltFee = floatval(substr($arrFee['fee'], 0, -1));
			} else {
				$fltFee = floatval($arrFee['fee']);
			}
			
			switch ($arrFee['calculation']) {
				case "percentShipping":
					$fltPercentage = $fltFee / 100;
					$fltCharge += $fltShipping * $fltPercentage;
				break;
				
				case "percentOrder":
					$fltPercentage = $fltFee / 100;
					$fltSubtotal = $objCollection->getSubtotal();
					$fltCharge += $fltSubtotal * $fltPercentage;
				break;
				
				case "perOrder":
					$fltCharge += $fltFee;
				break;
				
				case "perItem":
					$items = $objCollection->getItems();
					foreach($items as $objItem) {
						$fltCharge += ($fltFee * count($objItem->quantity));	
					}
				break;
				
				case "perBox":				
					$objPacker = new ShipmentPacker();
					$objPacker->setBoxSizes($this);
					$arrBoxes = $objPacker->packOrder($objCollection);
					$fltCharge += ($fltFee * count($arrBoxes));
				break;
			}
		}
		return $fltCharge;
    }
	
	
    /**
     * Get the checkout surcharge for this shipping method
     */
    public function getSurcharge(IsotopeProductCollection $objCollection)
    {
		
        return ProductCollectionSurcharge::createForShippingInCollection($this, $objCollection);
    }
	

    public function setService($strServiceCode, $objProductCollection = false)
    {
		if (class_exists('AvalaraIsotope\AvalaraApi') && $objProductCollection) {
			// Avalara Shipping Hook Here...
		}
		$this->strServiceCode = $strServiceCode;
    }
	
}
