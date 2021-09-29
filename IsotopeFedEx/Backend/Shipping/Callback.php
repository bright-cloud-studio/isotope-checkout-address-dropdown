<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */
 
 

namespace IsotopeFedEx\Backend\Shipping;

use Isotope\Backend\Shipping\Callback as IsotopeCallBack;
use Isotope\Model\Shipping;


class Callback extends IsotopeCallBack
{

    /**
     * Return the "boxes icon" button
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    public function boxesIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if ($row['type'] != 'fedex') {
            return '';
        }
        return '<a href="' . \Backend::addToUrl($href ."&amp;id=" .$row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
    }
	
   /**
     * Return the "new link" button
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    public function newLink($href, $label, $title, $icon, $attributes)
    {

        return '<a href="' . \Backend::addToUrl($href ."&amp;pid=" .\Input::get('id')) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
    }
	
	public function getServiceOptions() {
		return [
			'EUROPE_FIRST_INTERNATIONAL_PRIORITY' 	=> 'Europe First International Priority',
			'FEDEX_1_DAY_FREIGHT' 					=> 'FedEx 1 Day Freight',
			'FEDEX_2_DAY' 							=> 'FedEx 2 Day',
			'FEDEX_2_DAY_AM' 						=> 'FedEx 2 Day Am',
			'FEDEX_2_DAY_FREIGHT' 					=> 'FedEx 2 Day Freight',
			'FEDEX_3_DAY_FREIGHT' 					=> 'FedEx 3 Day Freight',
			'FEDEX_EXPRESS_SAVER' 					=> 'FedEx Express Saver',
			'FEDEX_FIRST_FREIGHT' 					=> 'FedEx First Freight',
			'FEDEX_FREIGHT_ECONOMY' 				=> 'FedEx Freight Economy',
			'FEDEX_FREIGHT_PRIORITY' 				=> 'FedEx Freight Priority',
			'FEDEX_GROUND' 							=> 'FedEx Ground',
			'FIRST_OVERNIGHT' 						=> 'First Overnight',
			'GROUND_HOME_DELIVERY' 					=> 'Ground Home Delivery',
			'INTERNATIONAL_ECONOMY' 				=> 'International Economy',
			'INTERNATIONAL_ECONOMY_FREIGHT' 		=> 'International Economy Freight',
			'INTERNATIONAL_FIRST' 					=> 'International First',
			'INTERNATIONAL_PRIORITY' 				=> 'International Priority',
			'INTERNATIONAL_PRIORITY_FREIGHT' 		=> 'International Priority Freight',
			'PRIORITY_OVERNIGHT' 					=> 'Priority Overnight',
			'SMART_POST' 							=> 'Smart Post',
			'STANDARD_OVERNIGHT' 					=> 'Standard Overnight'
		];
	}
	
}
