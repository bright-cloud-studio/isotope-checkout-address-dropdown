<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */



namespace IsotopeCheckoutAddressDropdown\CheckoutStep;

//use IsotopeFedEx\Model\Shipping\FedEx as FedExShippingModel;
//use IsotopeFedEx\AddressValidator;

use Contao\StringUtil;

use Isotope\CheckoutStep\BillingAddress;
use Isotope\Interfaces\IsotopeCheckoutStep;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;
use Isotope\Model\Address as AddressModel;
use Isotope\Module\Checkout;
use Model\Registry;

/**
 * BillingAddressDropdown checkout step lets the user enter a shipping address
 */
class BillingAddressDropdown extends BillingAddress implements IsotopeCheckoutStep
{

	 /**
	* Generate address options and return it as HTML string
	*
	* @param bool $blnValidate
	*
	* @return string
	*/
	protected function generateOptions($blnValidate = false)
    {
		
	// add custom js script to sync select and radio behavior
	if (!in_array('<script src="system/modules/isotope_checkout_address_dropdown/assets/js/isotope_checkout_address_dropdown.js"></script>', $GLOBALS['TL_BODY'])) { 
		$GLOBALS['TL_BODY'][] = '<script src="system/modules/isotope_checkout_address_dropdown/assets/js/isotope_checkout_address_dropdown.js"></script>';
	}
		
		
        $strBuffer  = '';
        $varValue   = '0';
        $arrOptions = $this->getAddressOptions();

        if (0 !== \count($arrOptions)) {
            foreach ($arrOptions as $option) {
                if ($option['default']) {
                    $varValue = $option['value'];
                }
            }

            $strClass  = $GLOBALS['TL_FFL']['select'];

            /** @var \Widget $objWidget */
            $objWidget = new $strClass(
                [
                    'id'          => $this->getStepClass(),
                    'name'        => $this->getStepClass(),
                    'mandatory'   => true,
                    'options'     => $arrOptions,
                    'value'       => $varValue,
                    'onchange'     => "Isotope.toggleAddressFields(this, '" . $this->getStepClass() . "_new');",
                    'storeValues' => true,
                    'tableless'   => true,
                ]
            );

            // Validate input
            if ($blnValidate) {
                $objWidget->validate();

                if ($objWidget->hasErrors()) {
                    $this->blnError = true;
                } else {
                    $varValue = (string) $objWidget->value;
                }
            } elseif ($objWidget->value != '') {
                \Input::setPost($objWidget->name, $objWidget->value);

                $objValidator = clone $objWidget;
                $objValidator->validate();

                if ($objValidator->hasErrors()) {
                    $this->blnError = true;
                }
            }

            $strBuffer .= $objWidget->parse();
        }

        if ($varValue !== '0') {
            $this->Template->style = 'display:none;';
        }

        $objAddress = $this->getAddressForOption($varValue, $blnValidate);

        if (null === $objAddress || !Registry::getInstance()->isRegistered($objAddress)) {
            $this->blnError = true;
        }  elseif ($blnValidate) {
            $this->setAddress($objAddress);
        }

        return $strBuffer . '<span><input type="radio" name="billingaddress" id="opt_billingaddress_71" class="radio" value="0" required="" onclick="Isotope.toggleAddressFields(this, &apos;billingaddressdropdown_new&apos;);"> <label id="lbl_billingaddress_71" for="opt_billingaddress_71">Create New Address</label></span>';
	//return 'ASDF';
    }
	
}
