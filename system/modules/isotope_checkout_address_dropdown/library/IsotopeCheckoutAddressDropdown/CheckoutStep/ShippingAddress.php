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

use IsotopeFedEx\Model\Shipping\FedEx as FedExShippingModel;
use IsotopeFedEx\AddressValidator;

use Contao\StringUtil;

use Isotope\CheckoutStep\ShippingAddress;
use Isotope\Interfaces\IsotopeCheckoutStep;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;
use Isotope\Model\Address as AddressModel;
use Isotope\Module\Checkout;


/**
 * ShippingAddressVerify checkout step lets the user enter a shipping address
 */
class ShippingAddressVerify extends ShippingAddress implements IsotopeCheckoutStep
{

	private $strApiKey;
	private $strApiPassword;
	private $strAccountNumber;
	private $strMeterNumber;
	private $strMode;

	function __construct(Checkout $objModule)
	{
		parent::__construct($objModule);
		if ($this->objModule) {
			$arrShippingModules = StringUtil::deserialize($this->objModule->iso_shipping_modules);
			$arrSearch = array(
				"column" => array(
					"tl_iso_shipping.id IN ('" .implode("','", $arrShippingModules) ."')", 
					"tl_iso_shipping.type LIKE 'fedex'"
			));
					
			$objShippingModule = FedExShippingModel::findAll($arrSearch);
			if ($objShippingModule) {
				foreach($objShippingModule->getModels() as $objModel) {
					if ($objModel->fedExApiKey && $objModel->fedExApiPassword && $objModel->fedExAccountNumber && $objModel->fedExMeterNumber && $objModel->fedExMode) {
						$this->strApiKey = $objModel->fedExApiKey;
						$this->strApiPassword = $objModel->fedExApiPassword;
						$this->strAccountNumber = $objModel->fedExAccountNumber;
						$this->strMeterNumber = $objModel->fedExMeterNumber;
						$this->strMode = $objModel->fedExMode;
					}
				}
			}
		}
	}


    /**
     * @inheritdoc
     */
    public function generate()
    {
        return parent::generate();
    }

    /**
     * @inheritdoc
     */
    public function review()
    {
        $objAddress = Isotope::getCart()->getDraftOrder()->getShippingAddress();

        if ($objAddress->id == Isotope::getCart()->getDraftOrder()->getBillingAddress()->id) {
            return false;
        }

        return array('shipping_address' => array
        (
            'headline' => $GLOBALS['TL_LANG']['MSC']['shipping_address'],
            'info'     => $objAddress->generate(Isotope::getConfig()->getShippingFieldsConfig()),
            'edit'     => $this->isSkippable() ? '' : Checkout::generateUrlForStep('address'),
        ));
    }

    /**
     * @inheritdoc
     */
    public function getNotificationTokens(IsotopeProductCollection $objCollection)
    {
        return array();
    }

    /**
     * Get options for all addresses in the user's address book
     *
     * @param array $arrFields
     *
     * @return array
     */
    protected function getAddressOptions($arrFields = null)
    {
        $arrOptions = array();

        if (FE_USER_LOGGED_IN === true) {

            /** @var AddressModel[] $arrAddresses */
            $arrAddresses = $this->getAddresses();
            $arrCountries = $this->getAddressCountries();

            if (0 !== \count($arrAddresses) && 0 !== \count($arrCountries)) {
                $objDefault = $this->getAddress();

                foreach ($arrAddresses as $objAddress) {

                    if (!\in_array($objAddress->country, $arrCountries, true)) {
                        continue;
                    }

                    $arrOptions[] = [
                        'value'   => $objAddress->id,
                        'label'   => $objAddress->generate($arrFields),
                        'default' => $objAddress->id == $objDefault->id ? '1' : '',
                    ];
                }
            }
        }
		
        array_unshift(
            $arrOptions,
            [
               'value'     => '-1',
               'label'     => Isotope::getCart()->requiresPayment() ? $GLOBALS['TL_LANG']['MSC']['useBillingAddress'] : $GLOBALS['TL_LANG']['MSC']['useCustomerAddress'],
               'default'   => '1',
            ]
        );

        $arrOptions[] = [
            'value'     => '0',
            'label'     => $GLOBALS['TL_LANG']['MSC']['differentShippingAddress'],
            'default'   => $this->getDefaultAddress()->id == Isotope::getCart()->shipping_address_id,
        ];

        return $arrOptions;
    }


    /**
     * Validate input and return address data
     *
     * @param bool $blnValidate
     *
     * @return array
     */
    protected function validateFields($blnValidate)
    {
        $arrAddress = array();
        $arrWidgets = $this->getWidgets();

        foreach ($arrWidgets as $strName => $objWidget) {
            // Validate input
            if ($blnValidate) {

                $objWidget->validate();
                $varValue = (string) $objWidget->value;

                // Convert date formats into timestamps
                if ('' !== $varValue && \in_array($objWidget->dca_config['eval']['rgxp'], array('date', 'time', 'datim'), true)) {
                    try {
                        $objDate = new \Date($varValue, $GLOBALS['TL_CONFIG'][$objWidget->dca_config['eval']['rgxp'] . 'Format']);
                        $varValue = $objDate->tstamp;
                    } catch (\OutOfBoundsException $e) {
                        $objWidget->addError(
                            sprintf(
                                $GLOBALS['TL_LANG']['ERR'][$objWidget->dca_config['eval']['rgxp']],
                                $GLOBALS['TL_CONFIG'][$objWidget->dca_config['eval']['rgxp'] . 'Format']
                            )
                        );
                    }
                }

                // Do not submit if there are errors
                if ($objWidget->hasErrors()) {
                    $this->blnError = true;
                } // Store current value
                elseif ($objWidget->submitInput()) {
                    $arrAddress[$strName] = $varValue;
                }

            } else {

                \Input::setPost($objWidget->name, $objWidget->value);

                $objValidator = clone $objWidget;
                $objValidator->validate();

                if ($objValidator->hasErrors()) {
                    $this->blnError = true;
                }
            }
        }
		
		if ($blnValidate && $arrAddress) {
			$objValidator = new AddressValidator($this->strApiKey, $this->strApiPassword, $this->strAccountNumber, $this->strMeterNumber, $this->strMode);
			$objValidator->validateAddress($arrAddress);
			if (!$objValidator->isValid()) {
				$this->blnError = true;	
			}
		}
		
        return $arrAddress;
    }



    /**
     * Get address object for a selected option
     *
     * @param mixed $varValue
     * @param bool  $blnValidate
     *
     * @return AddressModel
     */
    protected function getAddressForOption($varValue, $blnValidate)
    {
        if ($varValue === '-1') {
            return Isotope::getCart()->getBillingAddress();
        } elseif ($varValue === '0') {
            $objAddress = $this->getDefaultAddress();
            $arrAddress = $this->validateFields($blnValidate);

            if ($blnValidate) {
                foreach ($arrAddress as $field => $value) {
                    $objAddress->$field = $value;
                }

                $objAddress->save();
            }

            return $objAddress;
        }

        return parent::getAddressForOption($varValue, $blnValidate);
    }

    /**
     * Get default address for this collection and address type
     *
     * @return AddressModel
     */
    protected function getDefaultAddress()
    {
        $objAddress = AddressModel::findDefaultShippingForProductCollection(Isotope::getCart()->id);

        if (null === $objAddress) {
            $objAddress = AddressModel::createForProductCollection(
                Isotope::getCart(),
                Isotope::getConfig()->getShippingFields(),
                false,
                true
            );
        }

        return $objAddress;
    }

    /**
     * Get field configuration for this address type
     *
     * @return array
     */
    protected function getAddressFields()
    {
        return Isotope::getConfig()->getShippingFieldsConfig();
    }

    /**
     * Get allowed countries for this address type
     *
     * @return array
     */
    protected function getAddressCountries()
    {
        return Isotope::getConfig()->getShippingCountries();
    }

    /**
     * Get the current address (from Cart) for this address type
     *
     * @return AddressModel
     */
    protected function getAddress()
    {
        $billingAddress = Isotope::getCart()->getBillingAddress();
        $shippingAddress = Isotope::getCart()->getShippingAddress();

        if (null !== $shippingAddress
            && null !== $billingAddress
            && ($shippingAddress === $billingAddress
                || $shippingAddress->id < 1
            )
            && Isotope::getCart()->shipping_address_id != $billingAddress->id
        ) {
            Isotope::getCart()->setShippingAddress($billingAddress);
        } elseif (null !== $shippingAddress
            && Isotope::getCart()->shipping_address_id != $shippingAddress->id
        ) {
            Isotope::getCart()->setShippingAddress($shippingAddress);
        }

        return ($shippingAddress === $billingAddress) ? null : $shippingAddress;
    }

    /**
     * Set new address in cart
     *
     * @param AddressModel $objAddress
     */
    protected function setAddress(AddressModel $objAddress)
    {
		Isotope::getCart()->setShippingAddress($objAddress);
    }

}
