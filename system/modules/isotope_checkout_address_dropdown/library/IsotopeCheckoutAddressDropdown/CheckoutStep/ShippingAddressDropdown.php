<?php

/*
 * Isotope Checkout Address Dropdown - Changes addresses into dropdown selects
 *
 * Copyright (C) 2021 Bright Cloud Studio
 *
 * @package    bright-cloud-studio/isotope-checkout-address-dropdown
 * @link       https://www.brightcloudstudio.com/
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

namespace IsotopeCheckoutAddressDropdown\CheckoutStep;

use Contao\StringUtil;

use Isotope\CheckoutStep\ShippingAddress;
use Isotope\Interfaces\IsotopeCheckoutStep;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;
use Isotope\Model\Address as AddressModel;
use Isotope\Module\Checkout;
use Model\Registry;

/* Converts the addresses on checkout's Step 1 into a select, instead of radio buttons */
class ShippingAddressDropdown extends ShippingAddress implements IsotopeCheckoutStep
{

	/* Overrides the default generateOptions function to apply our changes */
	protected function generateOptions($blnValidate = false)
	{
		// this is the string we put the html into
		$strBuffer  = '';
		// this stores the Value
		$varValue   = '0';
		// this gets all of the addressess assigned to the user
		$arrOptions = $this->getAddressOptions();

		// if we have at least one value in $arrOptions
		if (0 !== \count($arrOptions)) {
			// loop through the options
			foreach ($arrOptions as $option) {
				// if the default option
				if ($option['default']) {
					$varValue = $option['value'];
				}
			}
			// create a new widget based on contao select
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

		 return $strBuffer . '<span><input type="radio" name="shippingaddress" id="opt_shippingaddress_71" class="radio" value="0" required="" onclick="Isotope.toggleAddressFields(this, &apos;shippingaddressdropdown_new&apos;);"> <label id="lbl_shippingaddress_71" for="opt_shippingaddress_71">Create New Address</label></span>';
		//return 'ASDF';
	}
	
}
