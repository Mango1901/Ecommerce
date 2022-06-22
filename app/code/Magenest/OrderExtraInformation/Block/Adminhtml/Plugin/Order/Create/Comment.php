<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Block\Adminhtml\Plugin\Order\Create;

use Magento\Customer\Model\ResourceModel\CustomerRepository;

/**
 * Class Comment
 * @package Magenest\OrderExtraInformation\Block\Adminhtml\Plugin\Order\Create
 */
class Comment
{
    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * Constructor.
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        CustomerRepository $customerRepository
    ) {
        $this->_customerRepository = $customerRepository;
    }

    /**
     * After to Html
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Comment $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(\Magento\Sales\Block\Adminhtml\Order\Create\Comment $subject, $result)
    {
        $defaultVATInvoice = [];
        try {
            $customer = $this->_customerRepository->getById($subject->getCustomerId());
            if ($customer->getId()) {
                if ($customer->getCustomAttribute('default_vat_invoice')) {
                    $defaultVATInvoice = \Zend_Json::decode($customer->getCustomAttribute('default_vat_invoice')->getValue());
                }
            }

            $defaultVATInvoice['require'] = false;
            $defaultVATInvoice['checked'] = !!$defaultVATInvoice['company_name'] ?? false;
            if (isset($customer)) {
                $defaultVATInvoice['require'] = true;
                $defaultVATInvoice['checked'] = $defaultVATInvoice['checked'] || $defaultVATInvoice['require'];
            }
        } catch (\Exception $e) {
        }

        return $result . $subject->getLayout()->createBlock(
            'Magento\Backend\Block\Template',
            'vatinvoice.order.info',
            ['data' => ['template' => 'Magenest_OrderExtraInformation::sales/order/create.phtml']]
        )
            ->setDefaultVATInvoice($defaultVATInvoice)
            ->toHtml();
    }
}
