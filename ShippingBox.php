<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */
 

 
namespace IsotopeFedEx\Model;

use DVDoug\BoxPacker\Box;
use JsonSerializable;


class ShippingBox extends \Model implements Box, JsonSerializable
{
	
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_iso_fedex_box';
	
	protected $arrPackedItems = array();
	
	public function scaleLengthUp($fltLength) {
		return intval($fltLength * 100);
	}
	
	public function scaleLengthDown($intLength) {
		return floatval($intLength) / 100;
	}
	
	public function scaleWeightUp($fltWeight) {
		return intval($fltWeight * 10000);
	}
	
	public function scaleWeightDown($intWeight) {
		return floatval($intWeight) / 10000;
	}
	
    public static function findPublishedByPid($intPid, array $arrOptions = array())
    {
        return static::findPublishedBy('pid', (int) $intPid, $arrOptions);
    }
	
    public static function findPublishedBy($arrColumns, $arrValues, array $arrOptions = array())
    {
        $t = static::$strTable;

        $arrValues = (array) $arrValues;

        if (!\is_array($arrColumns)) {
            $arrColumns = array(static::$strTable . '.' . $arrColumns . '=?');
        }

        if (BE_USER_LOGGED_IN !== true) {
            array_unshift(
                $arrColumns,
                "$t.published='1'"
            );
        }

        return static::findBy($arrColumns, $arrValues, $arrOptions);
    }
	
	public static function getWeightInPounds($fltWeight, $strUnit) {
		$fltReturn = 0.0000;
		switch ($strUnit) {
			case "g":
				$fltReturn = floatval($fltWeight) / 453.59237; 
			break 1;
			
			case "kg":
				$fltReturn = floatval($fltWeight) / 0.45359237; 
			break 1;
			
			case "oz":
				$fltReturn = floatval($fltWeight) / 16; 
			break 1;
			
			case "lb":
			default:
				$fltReturn = floatval($fltWeight); 
			break 1;
		}
		return $fltReturn;
	}
	
	public static function getLengthInInches($fltLength, $strUnit) {
		$fltReturn = 0.00;
		switch ($strUnit) {
			case "mm":
				$fltReturn = floatval($fltLength) / 25.4; 
			break 1;
			
			case "cm":
				$fltReturn = floatval($fltLength) / 2.54; 
			break 1;
			
			case "in":
			default:
				$fltReturn = floatval($fltLength); 
			break 1;
		}
		return $fltReturn;
	}
	
    public function getReference(): string
    {
        return $this->arrData['name'];
    }

    public function getOuterWidth(): int
    {
		$fltLength = static::getLengthInInches($this->arrData['outerWidth'], $this->arrData['uomDimensions']);
		return static::scaleLengthUp($fltLength);
    }

    public function getOuterLength(): int
    {
		$fltLength = static::getLengthInInches($this->arrData['outerLength'], $this->arrData['uomDimensions']);
		return static::scaleLengthUp($fltLength);
    }

    public function getOuterDepth(): int
    {
		$fltLength = static::getLengthInInches($this->arrData['outerHeight'], $this->arrData['uomDimensions']);
		return static::scaleLengthUp($fltLength);
    }

    public function getInnerWidth(): int
    {
		$fltLength = static::getLengthInInches($this->arrData['innerWidth'], $this->arrData['uomDimensions']);
		return static::scaleLengthUp($fltLength);
    }

    public function getInnerLength(): int
    {
		$fltLength = static::getLengthInInches($this->arrData['innerLength'], $this->arrData['uomDimensions']);
		return static::scaleLengthUp($fltLength);
    }

    public function getInnerDepth(): int
    {
		$fltLength = static::getLengthInInches($this->arrData['innerHeight'], $this->arrData['uomDimensions']);
		return static::scaleLengthUp($fltLength);
    }

    public function getEmptyWeight(): int
    {
		$fltWeight = static::getWeightInPounds($this->arrData['emptyWeight'], $this->arrData['uomDimensions']);
		return static::scaleWeightUp($fltWeight);
    }

    public function getMaxWeight(): int
    {
		$fltWeight = static::getWeightInPounds($this->arrData['maxWeight'], $this->arrData['uomDimensions']);
		return static::scaleWeightUp($fltWeight);
    }
	
    public function getTotalWeight($boolScale = true)
    {
		$fltWeight = $this->arrData['totalWeight'];
		if ($boolScale) {
			return static::scaleWeightUp($fltWeight);
		} else {
			return floatval($fltWeight);
		}
    }
	
    public function setTotalWeight($weight, $boolScaled = true)
    {
		if ($boolScaled) {
			$fltWeight = static::scaleWeightDown($weight);
		} else {
			$fltWeight = floatval($weight);
		}	
		$this->arrData['totalWeight'] = $fltWeight;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'reference' => $this->getReference(),
            'innerWidth' => $this->getInnerWidth(),
            'innerLength' => $this->getInnerLength(),
            'innerDepth' => $this->getInnerHeight(),
            'emptyWeight' => $this->getEmptyWeight(),
            'maxWeight' => $this->getMaxWeight(),
        ];
    }
	
    public function packItems($arrItems)
    {
		if ($arrItems) {
			$this->arrPackedItems = $arrItems;
        }
    }
	
    public function getPackedItems()
    {
		if ($this->arrPackedItems) {
			return $this->arrPackedItems;
        }
    }
	
}
