<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package	 isotope_shipping_fedex
 * @link	 https://andrewstevens.consulting
 */
 
 
 
/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_iso_shipping']['config']['ctable'][] = 'tl_iso_fedex_box';


/**
 * Operations
 */
$GLOBALS['TL_DCA']['tl_iso_shipping']['list']['operations']['boxes'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['boxes'],
	'icon'				=> 'system/modules/isotope_shipping_fedex/assets/images/box.png',
	'href'				=> 'table=tl_iso_fedex_box',
	'button_callback'	=> array('IsotopeFedEx\Backend\Shipping\Callback', 'boxesIcon')
);


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_iso_shipping']['palettes']['fedex'] = '{title_legend},name,label,type;{price_legend},tax_class;{note_legend:hide},note;{fedex_legend},fedExApiKey,fedExApiPassword,fedExAccountNumber,fedExMeterNumber,fedExMode,fedExAllowedServices;{ship_from_legend},shipFromStreet1,shipFromStreet2,shipFromStreet3,shipFromPostal,shipFromCity,shipFromSubdivision,shipFromCountry;{handling_legend},handlingFees;{config_legend},countries,subdivisions,postalCodes,quantity_mode,minimum_quantity,maximum_quantity,minimum_total,maximum_total,minimum_weight,maximum_weight,product_types,product_types_condition,config_ids;{expert_legend:hide},guests,protected;{enabled_legend},enabled';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['fedExApiKey'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExApiKey'],
	'search'				=> true,
	'inputType'				=> 'text',
	'eval'					=> array('maxlength' => 255, 'tl_class' => 'clr w50'),
	'sql'					=> "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['fedExApiPassword'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExApiPassword'],
	'inputType'				=> 'text',
	'eval'					=> array('maxlength' => 255, 'tl_class' => 'w50'),
	'sql'					=> "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['fedExAccountNumber'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExAccountNumber'],
	'search'				=> true,
	'inputType'				=> 'text',
	'eval'					=> array('maxlength' => 255, 'tl_class' =>'clr w50'),
	'sql'					=> "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['fedExMeterNumber'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExMeterNumber'],
	'search'				=> true,
	'inputType'				=> 'text',
	'eval'					=> array('maxlength' => 255, 'tl_class' =>'w50'),
	'sql'					=> "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['fedExMode'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExMode'],
	'inputType'             => 'select',
	'options'               => array('sandbox', 'production'),
	'eval'                  => array('mandatory'=>true, 'tl_class'=>'w50'),
	'sql'                   => "varchar(16) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['handlingFees'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_shipping']['handlingFees'],
	'inputType'             => 'multiColumnWizard',
	'eval' => array
	(
		'tl_class'          => 'clr',
		'dragAndDrop'       => true,
		'columnFields'      => array
		(
			'fee' => array
			(	
				'label'					=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['handlingFee'],
				'exclude'				=> true,
				'inputType'				=> 'text',
				'eval'					=> array('style'=>'width:300px', 'maxlength' => 16, 'rgxp' => 'surcharge', 'tl_class'=>'clr w50'),
			),
			'calculation' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['handlingCalculation'],
				'inputType'				=> 'select',
				'reference'             => &$GLOBALS['TL_LANG']['tl_iso_shipping'],
				'eval'					=> array('style'=>'width:300px', 'includeBlankOption' => true, 'tl_class' => 'w50'),
				'options'				=> array('percentShipping', 'percentOrder', 'perOrder', 'perItem', 'perBox'),
			),
		),
	),
	'sql'                   => "blob NULL",
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['fedExAllowedServices'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['fedExAllowedServices'],
	'inputType'			=> 'checkboxWizard',
	'eval'				=> array('multiple' => true, 'includeBlankOption'=>true, 'tl_class'=>'clr long'),
	'options_callback'	=> array('IsotopeFedEx\Backend\Shipping\Callback', 'getServiceOptions'),
	'sql'				=> "blob NULL"
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['shipFromStreet1'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromStreet1'],
	'inputType'             => 'text',
	'eval'                  => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                   => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['shipFromStreet2'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromStreet2'],
	'inputType'             => 'text',
	'eval'                  => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                   => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['shipFromStreet3'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromStreet3'],
	'inputType'             => 'text',
	'eval'                  => array('maxlength'=>255,'tl_class'=>'w50'),
	'sql'                   => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['shipFromPostal'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_shipping']['shipFromPostal'],

	'inputType'             => 'text',
	'eval'                  => array('mandatory'=>true, 'maxlength'=>32, 'tl_class'=>'clr w50'),
	'sql'                   => "varchar(32) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['shipFromCity'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_address']['shipFromCity'],
	'inputType'             => 'text',
	'eval'                  => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                   => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['shipFromSubdivision'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_address']['shipFromSubdivision'],
	'inputType'             => 'conditionalselect',
	'options_callback'      => array('Isotope\Backend', 'getSubdivisions'),
	'eval'                  => array('conditionField'=>'country', 'includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                   => "varchar(10) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['shipFromCountry'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_address']['country'],
	'inputType'             => 'select',
	'options'               => \System::getCountries(),
	'eval'                  => array('mandatory'=>true, 'tl_class'=>'w50', 'chosen'=>true),
	'sql'                   => "varchar(32) NOT NULL default ''",
);
