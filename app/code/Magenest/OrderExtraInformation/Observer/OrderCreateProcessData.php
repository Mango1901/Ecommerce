<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderExtraInformation\Observer;

use Magenest\OrderExtraInformation\Helper\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class OrderCreateProcessData
 *
 * @package Magenest\OrderExtraInformation\Observer
 */
class OrderCreateProcessData implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var Escaper
     */
    protected $_escaper;

    /**
     * Constructor.
     *
     * @param ManagerInterface $messageManager
     * @param Escaper $escaper
     */
    public function __construct(
        ManagerInterface $messageManager,
        Escaper $escaper
    ) {

        $this->_messageManager = $messageManager;
        $this->_escaper = $escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\AdminOrder\Create $model */
        $model = $observer->getEvent()->getOrderCreateModel();
        $orderData = $observer->getEvent()->getRequest();
        $quote = $model->getQuote();

        $wholesale = isset($orderData['order']) && isset($orderData['order']['is_wholesale_order']) && !empty($orderData['order']['is_wholesale_order']);
        $quote->setIsWholesaleOrder($wholesale);
        if ($orderData['order']['save_vat_invoice'] ?? false) {
            $quote->setCompanyName($orderData['order']['company_name'] ?? null);
            $quote->setTaxCode($orderData['order']['tax_code'] ?? null);
            $quote->setCompanyAddress($orderData['order']['company_address'] ?? null);

            $companyEmail = $orderData['order']['company_email'] ?? null;
            if (Validator::validateEmail($companyEmail)) {
                $quote->setCompanyEmail($companyEmail);
            }

        }

        if ($orderData['order']['save_customer_consign'] ?? false) {
            $quote->setTelephoneCustomerConsign($orderData['order']['telephone_customer_consign']);
        }

        return $this;
    }
}
