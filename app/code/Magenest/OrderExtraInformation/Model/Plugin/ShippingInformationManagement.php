<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderExtraInformation\Model\Plugin;

use Magenest\Core\Helper\Data as CoreData;
use Magenest\OrderExtraInformation\Helper\Validator;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class ShippingInformationManagement
 * @package Magenest\OrderExtraInformation\Model\Plugin
 */
class ShippingInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var CoreData
     */
    protected $_coreData;

    /**
     * Constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param CoreData $coreData
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        CoreData $coreData
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->_coreData = $coreData;
    }

    /**
     * Process before save address information
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $quote = $this->_quoteRepository->getActive($cartId);
        /** @var \Magento\Checkout\Api\Data\ShippingInformationExtension $extensionAttributes */
        $extensionAttributes = $addressInformation->getExtensionAttributes();
        $vatInvoice = $extensionAttributes->getVatInvoice();
        $this->clear($quote);

        if ($this->_coreData->getConfigValue('oei/customer_note/enable')) {
            $quote->setCustomerNote($extensionAttributes->getCustomerNote());
        }

        if ($this->_coreData->getConfigValue('oei/delivery_date/enable')) {
            $quote->setDeliveryDate($extensionAttributes->getDeliveryDate());
        }

        if ($this->_coreData->getConfigValue('oei/delivery_time/enable')) {
            $quote->setDeliveryTime($extensionAttributes->getDeliveryTime());
        }

        if ($this->_coreData->getConfigValue('oei/customer_consign/enable')) {
            $customerConsign = $extensionAttributes->getCustomerConsign();
            if ($customerConsign && $customerConsign->getSaveCustomerConsign()) {
                $quote->setTelephoneCustomerConsign($customerConsign->getTelephoneCustomerConsign());
            }
        }

        if ($this->_coreData->getConfigValue('oei/vat_invoice/enable')) {
            if (!is_null($vatInvoice)
                && $vatInvoice->getSaveVatInvoice()
            ) {
                $quote->setCompanyName($vatInvoice->getCompanyName());
                $quote->setTaxCode($vatInvoice->getTaxCode());
                $quote->setCompanyAddress($vatInvoice->getCompanyAddress());
                $companyEmail = $vatInvoice->getCompanyEmail() ?? null;
                if (Validator::validateEmail($companyEmail)) {
                    $quote->setCompanyEmail($companyEmail);
                }
            }
        }

        return [$cartId, $addressInformation];
    }

    /**
     * Clear
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     */
    public function clear($quote)
    {
        $quote->setCustomerNote(null);
        $quote->setCompanyName(null);
        $quote->setTaxCode(null);
        $quote->setCompanyAddress(null);
        $quote->setCompanyEmail(null);
    }
}
