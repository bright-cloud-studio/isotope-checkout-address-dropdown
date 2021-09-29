<?php

/**
 * FedEx Shipping Isotope eCommerce and Contao CMS
 *
 * Copyright (C) 2020 Andrew Stevens Consulting
 *
 * @package    isotope_shipping_fedex
 * @link       https://andrewstevens.consulting
 */



namespace IsotopeFedEx\CheckoutStep;

use IsotopeFedEx\Model\Shipping\FedEx;

use Isotope\CheckoutStep\CheckoutStep;
use Isotope\CheckoutStep\ShippingMethod;
use Isotope\Interfaces\IsotopeCheckoutStep;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Model\ProductCollectionSurcharge;
use Isotope\Model\Shipping;
use Isotope\Module\Checkout;
use Isotope\Isotope;
use Isotope\Template;

use Contao\StringUtil;


/**
 * ShippingMethod checkout step lets the user choose a shipping method.
 */
class FedExShippingMethod extends ShippingMethod implements IsotopeCheckoutStep
{
    /**
     * Shipping modules.
     * @var array
     */
    private $modules;

    /**
     * Shipping options.
     * @var array
     */
    private $options;

    /**
     * Returns true if the current cart has shipping
     *
     * @inheritdoc
     */
    public function isAvailable()
    {
        $available = Isotope::getCart()->requiresShipping();

        if (!$available) {
            Isotope::getCart()->setShippingMethod(null);
        }

        return $available;
    }

    /**
     * Skip the checkout step if only one option is available
     *
     * @inheritdoc
     */
    public function isSkippable()
    {
        if (!$this->objModule->canSkipStep('shipping_method')) {
            return false;
        }

        $this->initializeModules();

        return 1 === \count($this->options);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
		$session = \Session::getInstance();
		
        $this->initializeModules();

        if (empty($this->modules)) {
            $this->blnError = true;

            \System::log('No shipping methods available for cart ID ' . Isotope::getCart()->id, __METHOD__, TL_ERROR);

            /** @var Template|\stdClass $objTemplate */
            $objTemplate           = new Template('mod_message');
            $objTemplate->class    = 'shipping_method';
            $objTemplate->hl       = 'h2';
            $objTemplate->headline = $GLOBALS['TL_LANG']['MSC']['shipping_method'];
            $objTemplate->type     = 'error';
            $objTemplate->message  = $GLOBALS['TL_LANG']['MSC']['noShippingModules'];

            return $objTemplate->parse();
        }

        /** @var \Widget $objWidget */
        $objWidget = new $GLOBALS['TL_FFL']['select'](
            [
                'id'          => $this->getStepClass(),
                'name'        => $this->getStepClass(),
                'mandatory'   => true,
                'options'     => $this->options,
                'value'       => Isotope::getCart()->shipping_id,
                'storeValues' => true,
                'tableless'   => true,
            ]
        );

        // If there is only one shipping method, mark it as selected by default
        if (\count($this->modules) === 1) {
            $objModule        = reset($this->modules);
            $objWidget->value = $objModule->id;
            Isotope::getCart()->setShippingMethod($objModule);
        }

        if (\Input::post('FORM_SUBMIT') == $this->objModule->getFormId()) {
            $objWidget->validate();

            if (!$objWidget->hasErrors()) {
				list($intModule, $strType, $strService) = explode(':', $objWidget->value, 3);
				if (!$strType) {
					Isotope::getCart()->setShippingMethod($this->modules[$objWidget->value]);
				} else {
					if ($strType == 'FEDEX') {
						$objModule = $this->modules[$intModule];
						$objModule->setService($strType .':' .$strService, Isotope::getCart());
						$session->set('fedex_shipping_service', $strType .':' .$strService);
						Isotope::getCart()->setShippingMethod($objModule);

					}
				}
            }		
		}

        if (!Isotope::getCart()->hasShipping() || !isset($this->modules[Isotope::getCart()->shipping_id])) {
            $this->blnError = true;
        }

        /** @var Template|\stdClass $objTemplate */
        $objTemplate                  = new Template('iso_checkout_shipping_method');
        $objTemplate->headline        = $GLOBALS['TL_LANG']['MSC']['shipping_method'];
        $objTemplate->message         = $GLOBALS['TL_LANG']['MSC']['shipping_method_message'];
        $objTemplate->options         = $objWidget->parse();
        $objTemplate->shippingMethods = $this->modules;

        return $objTemplate->parse();
    }

    /**
     * @inheritdoc
     */
    public function review()
    {
        return array(
            'shipping_method' => array(
                'headline' => $GLOBALS['TL_LANG']['MSC']['shipping_method'],
                'info'     => Isotope::getCart()->getDraftOrder()->getShippingMethod()->checkoutReview(),
                'note'     => Isotope::getCart()->getDraftOrder()->getShippingMethod()->getNote(),
                'edit'     => $this->isSkippable() ? '' : Checkout::generateUrlForStep('shipping'),
            ),
        );
    }

    /**
     * Initialize modules and options
     */
    private function initializeModules()
    {		
        if (null !== $this->modules && null !== $this->options) {
            return;
        }

        $this->modules = array();
        $this->options = array();

        $arrIds = deserialize($this->objModule->iso_shipping_modules);

        if (!empty($arrIds) && \is_array($arrIds)) {
            $arrColumns = array('id IN (' . implode(',', $arrIds) . ')');

            if (true !== BE_USER_LOGGED_IN) {
                $arrColumns[] = "enabled='1'";
            }

            /** @var Shipping[] $objModules */
            $objModules = Shipping::findBy(
                $arrColumns, null, array('order' => \Database::getInstance()->findInSet('id', $arrIds))
            );

            if (null !== $objModules) {
                foreach ($objModules as $objModule) {

                    if (!$objModule->isAvailable()) {
                        continue;
                    }
					
					if (is_a($objModule, 'IsotopeFedEx\Model\Shipping\FedEx')) {
						$arrRates = $objModule->getRates(Isotope::getCart());
						
						$strLabel = $objModule->getLabel();
						
						$arrAllowedServices = $objModule->fedExAllowedServices;
						
						if ($arrAllowedServices && !is_array($arrAllowedServices)) {
							$arrAllowedServices = StringUtil::deserialize($arrAllowedServices);
						}
						foreach($arrAllowedServices as $intIndex => $strCode) {
							if (substr($strCode, 0, 6) != "FEDEX:") {
								$arrAllowedServices[$intIndex] = "FEDEX:" .$strCode;
							}
						}
						
						if ($arrAllowedServices) {
							foreach($arrAllowedServices as $strCode) {						
								//foreach ($arrRates as $strCode => $arrRated) {
								$arrRated = $arrRates[$strCode];
								if ($arrRated) {
									$fltPrice = $arrRated['price'];
									
									$strLabel = $arrRated['name'];
									$strLabel .= ': ' . Isotope::formatPriceWithCurrency($fltPrice);

									$this->options[] = array(
										'value' => $objModule->id .":" .$strCode,
										'label' => $strLabel
									);									
								}
							}
						} else {
							foreach ($arrRates as $strCode => $arrRated) {
								if ($arrRated) {
									$fltPrice = $arrRated['price'];
									
									$strLabel = $arrRated['name'];
									$strLabel .= ': ' . Isotope::formatPriceWithCurrency($fltPrice);

									$this->options[] = array(
										'value' => $objModule->id .":" .$strCode,
										'label' => $strLabel
									);
								}
							}
						}
					} else {
						$strLabel = $objModule->getLabel();
						$fltPrice = $objModule->getPrice();

						if ($fltPrice != 0) {
							if ($objModule->isPercentage()) {
								$strLabel .= ' (' . $objModule->getPercentageLabel() . ')';
							}

							$strLabel .= ': ' . Isotope::formatPriceWithCurrency($fltPrice);
						}

						if ($note = $objModule->getNote()) {
							$strLabel .= '<span class="note">' . $note . '</span>';
						}

						$this->options[] = array(
							'value' => $objModule->id,
							'label' => $strLabel,
						);
					}
					
					if (empty($this->options)) {
						$this->blnError = true;	
					}
					$this->modules[$objModule->id] = $objModule;
                }
            }
        }
    }
	
    /**
     * @inheritdoc
     */
    public function getNotificationTokens(IsotopeProductCollection $objCollection)
    {
        return array();
    }

}
