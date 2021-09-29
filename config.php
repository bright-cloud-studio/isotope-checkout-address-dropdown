<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */



/**
 * Backend Modules
 */
$GLOBALS['BE_MOD']['isotope']['iso_setup']['tables'][] 		= 'tl_iso_fedex_box';


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_iso_fedex_box'] 					= 'IsotopeFedEx\Model\ShippingBox';


/**
 * Contao Hooks
 */
$GLOBALS['ISO_HOOKS']['preCheckout'][] 						= array('\IsotopeFedEx\AddressValidator', 'validateAddress'); 
//$GLOBALS['ISO_HOOKS']['useTaxRate'][] 						= array('\IsotopeFedEx\FedExUtility', 'taxRateInfo'); 

/**
 * Isotope Modules
 */
$GLOBALS['ISO_MOD']['checkout']['shipping']['tables'][] 	= 'tl_iso_fedex_box';


/**
 * Shipping Methods
 */
\Isotope\Model\Shipping::registerModelType('fedex', 'IsotopeFedEx\Model\Shipping\FedEx');


/**
 * Checkout steps
 */
foreach ($GLOBALS['ISO_CHECKOUTSTEP']['address'] as $index => $value) {
	if ($value == '\Isotope\CheckoutStep\ShippingAddress' || $value == 'Isotope\CheckoutStep\ShippingAddress') {
		$GLOBALS['ISO_CHECKOUTSTEP']['address'][$index] = '\IsotopeFedEx\CheckoutStep\ShippingAddressVerify';
	}
}
foreach ($GLOBALS['ISO_CHECKOUTSTEP']['shipping'] as $index => $value) {
	if ($value == '\Isotope\CheckoutStep\ShippingMethod' || $value == 'Isotope\CheckoutStep\ShippingMethod' ) {
		$GLOBALS['ISO_CHECKOUTSTEP']['shipping'][$index] = '\IsotopeFedEx\CheckoutStep\FedExShippingMethod';
	}
}
