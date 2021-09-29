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

use Isotope\CheckoutStep\ShippingAddress;
use Isotope\Interfaces\IsotopeCheckoutStep;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;
use Isotope\Model\Address as AddressModel;
use Isotope\Module\Checkout;
use Model\Registry;

/**
 * ShippingAddressDropdown checkout step lets the user enter a shipping address
 */
class ShippingAddressDropdown extends ShippingAddress implements IsotopeCheckoutStep
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
                    'onclick'     => "Isotope.toggleAddressFields(this, '" . $this->getStepClass() . "_new');",
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

        return $strBuffer;
	//return 'ASDF';
    }
	
}
