<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */
 

 
declare(strict_types=1);

namespace IsotopeFedEx;

use DVDoug\BoxPacker\Item;
use JsonSerializable;
use stdClass;


class PackedItem implements Item, JsonSerializable
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $depth;

    /**
     * @var int
     */
    private $weight;

    /**
     * @var int
     */
    private $keepFlat;

    /**
     * Test objects that recurse.
     *
     * @var stdClass
     */
    private $a;

    /**
     * Test objects that recurse.
     *
     * @var stdClass
     */
    private $b;
	
	private $objProductCollectionItem;

    /**
     * TestItem constructor.
     */
    public function __construct(
        string $description,
        int $width,
        int $length,
        int $depth,
        int $weight,
        bool $keepFlat)
    {
        $this->description = $description;
        $this->width = $width;
        $this->length = $length;
        $this->depth = $depth;
        $this->weight = $weight;
        $this->keepFlat = $keepFlat;

        $this->a = new stdClass();
        $this->b = new stdClass();

        $this->a->b = $this->b;
        $this->b->a = $this->a;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getKeepFlat(): bool
    {
        return $this->keepFlat;
    }
	
    public function getProductCollectionItem()
    {
		return $this->objProductCollectionItem;
    }
	
    public function setProductCollectionItem($objProductCollectionItem)
    {
		$this->objProductCollectionItem = $objProductCollectionItem;
    }
	
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'description' => $this->description,
            'width' => $this->width,
            'length' => $this->length,
            'depth' => $this->depth,
            'weight' => $this->weight,
            'keepFlat' => $this->keepFlat,
        ];
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
	
}
