<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */



namespace IsotopeFedEx;




class FedExUtility
{
	
	public function taxRateInfo($objTaxRate, $fltPrice, $arrAddresses) {		
		if ($objTaxRate->id == 100008) {
			echo "<h4>Tax Rate Object</h4>";
			var_dump($objTaxRate);
			echo "<hr>";
			
			echo "<h4>Price</h4>";
			var_dump($fltPrice);
			echo "<hr>";
			
			echo "<h4>Addresses</h4>";
			var_dump($arrAddresses);
			echo "<hr>";
		}
		
		return true;
	}
	
}
