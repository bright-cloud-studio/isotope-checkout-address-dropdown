<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */


/* Checkout steps */
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
