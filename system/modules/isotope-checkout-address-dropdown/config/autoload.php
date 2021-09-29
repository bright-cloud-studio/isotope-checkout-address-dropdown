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
 * Register the classes
 */

ClassLoader::addClasses(array
(
	
	'IsotopeFedEx\AddressValidator' 							=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/AddressValidator.php',
	'IsotopeFedEx\PackedItem' 									=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/PackedItem.php',
	'IsotopeFedEx\ShipmentPacker' 								=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/ShipmentPacker.php',
	'IsotopeFedEx\FedExUtility' 								=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/FedExUtility.php',
	
	'IsotopeFedEx\Backend\Boxes' 								=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/Backend/Boxes.php',
	'IsotopeFedEx\Backend\Shipping\Callback' 					=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/Backend/Shipping/Callback.php',
	
	'IsotopeFedEx\CheckoutStep\ShippingAddressVerify' 			=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/CheckoutStep/ShippingAddressVerify.php',
	'IsotopeFedEx\CheckoutStep\FedExShippingMethod' 			=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/CheckoutStep/FedExShippingMethod.php',

	'IsotopeFedEx\Model\ShippingBox' 							=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/Model/ShippingBox.php',	
	'IsotopeFedEx\Model\Shipping\FedEx' 						=> 'system/modules/isotope_shipping_fedex/library/IsotopeFedEx/Model/Shipping/FedEx.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'fedex_shipping_details'									=> 'system/modules/isotope_shipping_fedex/templates/backend',
));
