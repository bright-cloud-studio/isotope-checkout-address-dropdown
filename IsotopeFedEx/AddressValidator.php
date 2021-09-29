<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */



namespace IsotopeFedEx;

use Contao\FormModel;
use Contao\FormFieldModel;

use Isotope\Model\Address as AddressModel;

use FedEx\AddressValidationService\Request;
use FedEx\AddressValidationService\ComplexType;
use FedEx\AddressValidationService\SimpleType;


class AddressValidator
{
	
	var $boolAmbiguousAddress;
	var $boolInvalidAddress;
	var $boolValidAddress;
	var $boolError;
	
	var $strDebug;
	
	var $arrValidatedAddress;
	var $arrSuggestions = array();
	
	private $strApiKey;
	private $strApiPassword;
	private $strAccountNumber;
	private $strMeterNumber;
	private $strMode;
	
	function __construct($strApiKey = null, $strApiPassword= null, $strAccountNumber = null, $strMeterNumber = null, $strMode = 'sandbox') {
		$this->strApiKey = $strApiKey;
		$this->strApiPassword = $strApiPassword;
		$this->strAccountNumber = $strAccountNumber;
		$this->strMeterNumber = $strMeterNumber;
		$this->strMode = $strMode;
	}

	function validateAddress($address) {
		
		if (!$address) {
			return false;
		}
		
		if (is_a($address, 'Isotope\Model\Address')) {
			$address = $address->row();
		} else if (is_string($address)) {
			// Parse a string address
		} else {
			return;
		}
		
		if (stristr($address['subdivision'], '-') !== false) {
			list($strCountryCode, $strStateAbbreviation) = explode('-', $address['subdivision'], 2); 
		} else {
			$strStateAbbreviation = $address['subdivision'];
		}
		

		$objAddressValidationRequest = new ComplexType\AddressValidationRequest();

		$objAddressValidationRequest->WebAuthenticationDetail->UserCredential->Key = $this->strApiKey;
		$objAddressValidationRequest->WebAuthenticationDetail->UserCredential->Password = $this->strApiPassword;
		$objAddressValidationRequest->ClientDetail->AccountNumber = $this->strAccountNumber;
		$objAddressValidationRequest->ClientDetail->MeterNumber = $this->strMeterNumber;

		$objAddressValidationRequest->Version->ServiceId = 'aval';
		$objAddressValidationRequest->Version->Major = 4;
		$objAddressValidationRequest->Version->Intermediate = 0;
		$objAddressValidationRequest->Version->Minor = 0;

		$objAddressValidationRequest->AddressesToValidate = [new ComplexType\AddressToValidate()];
		$objAddressValidationRequest->AddressesToValidate[0]->Address->StreetLines = [$address['street_1'], $address['street_2'], $address['street_3']];
		$objAddressValidationRequest->AddressesToValidate[0]->Address->City = $address['city'];
		$objAddressValidationRequest->AddressesToValidate[0]->Address->StateOrProvinceCode = $strStateAbbreviation;
		$objAddressValidationRequest->AddressesToValidate[0]->Address->PostalCode = $address['postal'];
		$objAddressValidationRequest->AddressesToValidate[0]->Address->CountryCode = $address['country'];

		$objRequest = new Request();
		if ($this->strMode == 'production') {
			$objRequest->getSoapClient()->__setLocation(Request::PRODUCTION_URL);
		} else {
			$objRequest->getSoapClient()->__setLocation(Request::TESTING_URL);
		}
		$objAddressValidationReply = $request->getAddressValidationReply($objAddressValidationRequest);
		
var_dump($objAddressValidationReply);
die();
		
		$boolInvalidAddress = true;
		
		
		
/*
		
		try {
			$objResponse = $objFedExAddressValidation->validate($objFedExAddress, $requestOption = FedExAddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION, $maxSuggestion = 15);
			
			if ($objResponse->isAmbiguous()) {
				$arrCandidateAddresses = $objResponse->getCandidateAddressList();
				$boolInvalidAddress = false;
				foreach($arrCandidateAddresses as $arrCandidate) {
					$this->arrSuggestions[] = (array)$arrCandidate;   
				}
			}
			
			if ($objResponse->isValid()) {
				$this->arrValidatedAddress = (array)$objResponse->getValidatedAddress();
				$boolInvalidAddress = false;
				$boolValidated = true;
			}
		} catch (Exception $e) {
			$this->boolError = true;
			$boolInvalidAddress = false;
			$this->strDebug .= print_r($e, true);
		}
*/		
		return !($boolInvalidAddress);
	}
	
	public function isValid() {
		return !boolval($this->boolInvalidAddress);
	}
	
}
