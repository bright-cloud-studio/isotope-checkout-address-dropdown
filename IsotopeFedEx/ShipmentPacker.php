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

use IsotopeFedEx\PackedBox;
use IsotopeFedEx\PackedItem;
use IsotopeFedEx\Model\ShippingBox;
use IsotopeFedEx\Model\Shipping\FedEx;

use Contao\Database;
		
use DVDoug\BoxPacker\Packer;

use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;


class ShipmentPacker
{

	var $arrShippingBox = array();
	var $arrPackedBoxes = array();
	
	var $strError;
	var $strDebug;
	
	function setBoxSizes($shippingMethod) {
		if (is_int($shippingMethod)) {
			$objShippingMethod = FedEx::findByPk($shippingMethod);
			if (!$objShippingMethod) {
				$this->strError = 'No shipping method found with id: ' .$shippingMethod;
				return false;
			}
		} else if (is_a($shippingMethod, 'IsotopeFedEx\Model\Shipping\FedEx')) {
			$objShippingMethod = $shippingMethod;
		}
		
		$objShippingBox = ShippingBox::findPublishedByPid($objShippingMethod->id);
		
		if (!$objShippingBox) {
			$this->strError = 'No boxes found for shipping method with id: ' .$shippingMethod;
			return false;
		}
		
		$this->arrShippingBox = $objShippingBox->getModels();
		
		return $this->arrShippingBox;
	}
	
	public function packOrder($productCollection) {
		if (is_int($productCollection)) {
			$objProductCollection = FedEx::findByPk($productCollection);
			if (!$objProductCollection) {
				$this->strError = 'No product collection found with id: ' .$productCollection;
				return false;
			}
		} else if (is_a($productCollection, 'IsotopeProductCollection')) {
			$objProductCollection = $productCollection;
		} else {
			$objProductCollection = Isotope::getCart();
		}
		
		$arrPackableItems = array();
		$arrUnpackableItems = array();
		$arrItems = array();
		$fltTotalVolume = 0.00;
		$fltTotalWeight = 0.00;

		$objPacker = new Packer();

		foreach($this->arrShippingBox as $intIndex => $objShippingBox) {
			$objPacker->addBox($objShippingBox);
		}
		
		$arrCollectionItems = $objProductCollection->getItems();
		foreach($arrCollectionItems as $objItem) {
			$objProduct = $objItem->getProduct();
			if (!$objProduct) {
				continue;
			}

			$fltWeight = PackedItem::getWeightInPounds($objProduct->shipping_weight, $objProduct->shipping_uom_weight);
			$fltHeight = PackedItem::getLengthInInches($objProduct->shipping_height, $objProduct->shipping_uom_dimensions);
			$fltWidth = PackedItem::getLengthInInches($objProduct->shipping_width, $objProduct->shipping_uom_dimensions);
			$fltLength = PackedItem::getLengthInInches($objProduct->shipping_length, $objProduct->shipping_uom_dimensions);
			
			$objPackedItem = new PackedItem(
				$objProduct->id, 
				intval($fltWidth * 100), 
				intval($fltLength * 100), 
				intval($fltHeight * 100), 
				intval($fltWeight * 10000), 
				($objProduct->shipping_keep_flat ? true : false)
			);
			$objPackedItem->setProductCollectionItem($objItem);
			
			$objPacker->addItem(
				$objPackedItem,
				$objItem->quantity
			);
		}

		$arrPacked = array();
		try {
			$arrPacked = $objPacker->pack();
		} catch (Exception $e) {
			$this->strDebug .= print_r($e, true);
			return false;
		}
		
		$arrBoxes = array();
		foreach($arrPacked as $objPackedBox) {
			
			$arrPackedItems = $objPackedBox->getItems();
			
			$objShippingBox = $objPackedBox->getBox();
			$objShippingBox->setTotalWeight($objPackedBox->getWeight(), true);
			
			$arrItems = array();
			foreach($arrPackedItems as $arrItem) {
				$arrItems[] = $arrItem->getItem();
			}
			
			$objShippingBox->packItems($arrItems);
			
			$arrBoxes[] = $objShippingBox;
		}
		
		$this->arrPackedBoxes = $arrBoxes;
		
		return $this->arrPackedBoxes;
	}
}
