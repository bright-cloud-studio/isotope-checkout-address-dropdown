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
 * Operations
 */
$GLOBALS['TL_LANG']['tl_iso_shipping']['boxes'] 				= array('Boxes', 'Configure boxes sizes for this shipping method.');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_iso_shipping']['fedex_legend'] 			= 'FedEx API Configuration';
$GLOBALS['TL_LANG']['tl_iso_shipping']['handling_legend'] 		= 'Handling Fees';
$GLOBALS['TL_LANG']['tl_iso_shipping']['ship_from_legend'] 		= 'Origin Address';


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExApiKey'] 			= array('API Key', 'FedEx API key.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExApiPassword'] 		= array('API Password', 'FedEx API Password.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExAccountNumber'] 	= array('Account Number', 'FedEx account number.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExMeterNumber'] 		= array('Meter Number', 'FedEx meter number.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExMode'] 			= array('API Mode', 'Sandbox or production server.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['handlingFees'] 			= array('Handling Fees', 'Handling fees and how to assess them.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['handlingFee'] 			= array('Handling Fee', 'Handling fee.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['handlingCalculation'] 	= array('Handling Calculation', 'How handling fee is assessed.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExAllowedServices'] 	= array('FedEx Services', 'Select which FedEx services are available to this shipping module.');

$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromStreet1'] 		= array('Street Address 1', 'Street Address 1 of origin (ship from) address.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromStreet2'] 		= array('Street Address 2', 'Street Address 2 of origin (ship from) address.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromStreet3'] 		= array('Street Address 3', 'Street Address 2 of origin (ship from) address.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromPostal'] 		= array('Postal Code', 'Postal Code of origin (ship from) address.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromCity'] 			= array('City', 'City of origin (ship from) address.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromSubdivision'] 	= array('State', 'State/Region of origin (ship from) address.');
$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromCountry'] 		= array('Country', 'Country of origin (ship from) address.');
/**
 * References
 */
$GLOBALS['TL_LANG']['tl_iso_shipping']['percentShipping'] 		= 'Percent of shipping total';
$GLOBALS['TL_LANG']['tl_iso_shipping']['percentOrder'] 			= 'Percent of order total';
$GLOBALS['TL_LANG']['tl_iso_shipping']['perBox'] 				= 'Per box shipped';
$GLOBALS['TL_LANG']['tl_iso_shipping']['perOrder'] 				= 'Per order';
