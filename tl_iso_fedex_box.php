<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package	   isotope_shipping_fedex
 * @link	   https://andrewstevens.consulting
 */
 
 

/**
 * Table tl_iso_fedex_box
 */
$GLOBALS['TL_DCA']['tl_iso_fedex_box'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'			   => 'Table',
		'enableVersioning'		   => true,
		'ptable'				   => 'tl_iso_shipping',
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
			)
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(

			'mode'					=> 2,
			'fields'				=> array('name', 'outerHeight', 'outerWidth', 'outerLength', 'innerHeight', 'innerWidth', 'innerLength', 'maxWeight', 'emptyWeight', 'uomWeight', 'boxCharge'),
			'flag'					=> 1,
			'panelLayout'			=> 'filter;search,limit'
		),
		'label' => array
		(
			'fields'				=> array('name', 'outerHeight', 'outerWidth', 'outerLength', 'innerHeight', 'innerWidth', 'innerLength', 'uomDimensions', 'maxWeight', 'emptyWeight', 'uomWeight', 'boxCharge'),
			'showColumns'			=> true
		),
		'global_operations' => array
		(
			'new' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fed_box']['new'],
				'href'				=> 'act=create',
				'class'				=> 'header_new',
				'attributes'		=> 'onclick="Backend.getScrollOffset();"',
				'button_callback'	=> array('IsotopeFedEx\Backend\Shipping\Callback', 'newLink')
			),
			'all' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'				=> 'act=select',
				'class'				=> 'header_edit_all',
				'attributes'		=> 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['edit'],
				'href'				=> 'act=edit',
				'icon'				=> 'edit.gif'
			),
			'copy' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['copy'],
				'href'				=> 'act=copy',
				'icon'				=> 'copy.gif',
			),
			'delete' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['delete'],
				'href'				=> 'act=delete',
				'icon'				=> 'delete.gif',
				'attributes'		=> 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			),
			'toggle' => array
			(
				'icon'                => 'visible.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('IsotopeFedEx\Backend\Boxes', 'toggleIcon')
			),
			'show' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['show'],
				'href'				=> 'act=show',
				'icon'				=> 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'				=> '{name_legend},name;{dimensions_legend},outerHeight,outerWidth,outerLength,innerHeight,innerWidth,innerLength,uomDimensions;{weight_legend},maxWeight,emptyWeight,uomWeight;{surcharge_legend},boxCharge;',
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'				=>	"int(10) unsigned NOT NULL auto_increment",
		),
		'pid' => array
		(
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'				=>	"int(10) unsigned NOT NULL default '0'",
		),
		'name' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['name'],
			'search'			=> true,
			'inputType'			=> 'text',
			'eval'				=> array('maxlength'=>255, 'mandatory'=>true, 'tl_class'=>'w50'),
			'sql'				=> "varchar(255) NOT NULL default ''",
		),
		'outerHeight' => array
		(	
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['outerHeight'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'digit', 'minval'=>0, 'maxval'=>9999, 'tl_class'=>'w50 clr'),
			'sql'				=> "varchar(10) NOT NULL default ''"
		),
		'outerWidth' => array
		(	
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['outerWidth'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'digit', 'minval'=>0, 'maxval'=>9999, 'tl_class'=>'w50'),
			'sql'				=> "varchar(10) NOT NULL default ''"
		),
		'outerLength' => array
		(	
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['outerLength'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'digit', 'minval'=>0, 'maxval'=>9999, 'tl_class'=>'w50 clr'),
			'sql'				=> "varchar(10) NOT NULL default ''"
		),
		'innerHeight' => array
		(	
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['innerHeight'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'digit', 'minval'=>0, 'maxval'=>9999, 'tl_class'=>'w50 clr'),
			'sql'				=> "varchar(10) NOT NULL default ''"
		),
		'innerWidth' => array
		(	
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['innerWidth'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'digit', 'minval'=>0, 'maxval'=>9999, 'tl_class'=>'w50'),
			'sql'				=> "varchar(10) NOT NULL default ''"
		),
		'innerLength' => array
		(	
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['innerLength'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'digit', 'minval'=>0, 'maxval'=>9999, 'tl_class'=>'w50 clr'),
			'sql'				=> "varchar(10) NOT NULL default ''"
		),
		'uomDimensions' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['uomDimensions'],
			'inputType'			=> 'select',
			'eval'				=> array('includeBlankOption'=>true, 'tl_class'=>'clr w50'),
			'reference'			=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box'],
			'options'			=> array('in', 'mm', 'cm'),
			'sql'				=> "varchar(32) NOT NULL default ''"
		),
		'maxWeight' => array
		(	
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['maxWeight'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'digit', 'minval'=>0, 'maxval'=>99999, 'tl_class'=>'w50 clr'),
			'sql'				=> "varchar(10) NOT NULL default ''"
		),
		'emptyWeight' => array
		(	
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['emptyWeight'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'digit', 'minval'=>0, 'maxval'=>99999, 'tl_class'=>'w50'),
			'sql'				=> "varchar(10) NOT NULL default ''"
		),
		'uomWeight' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['uomWeight'],
			'inputType'			=> 'select',
			'eval'				=> array('includeBlankOption'=>true, 'tl_class'=>'clr w50'),
			'reference'			=> &$GLOBALS['TL_LANG']['WGT'],
			'options'			=> array('lb', 'oz', 'kg', 'g'),
			'sql'				=> "varchar(32) NOT NULL default ''"
		),
		'boxCharge' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_iso_fedex_box']['boxCharge'],
			'inputType'			=> 'text',
			'eval'				=> array('maxlength'=>13, 'rgxp'=>'price', 'tl_class'=>'w50'),
			'sql'				=> "decimal(12,2) NOT NULL default '0.00'",
		),
		'published' => array
		(
			'filter'			=> true,
			'inputType'			=> 'checkbox',
			'eval'				=> array('doNotCopy'=>true),
			'sql'				=> "char(1) NOT NULL default ''"
		),
	)
);
