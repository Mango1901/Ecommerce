<?php

namespace Magenest\OrderExtraInformation\Block\Adminhtml\Plugin\Order\Create\Billing;

use Magento\Backend\Block\Template;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Psr\Log\LoggerInterface;
use Magento\Framework\ObjectManagerInterface;


/**
 * Class Address
 */
class Address
{
    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Address constructor.
     * @param CustomerRepository $customerRepository
     * @param LoggerInterface $logger
     * @param ObjectManagerInterface $objectManager
     */
    function __construct(
        CustomerRepository $customerRepository,
        LoggerInterface $logger,
        ObjectManagerInterface $objectManager
    ) {
        $this->_customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->_objectManager = $objectManager;
    }

    /**
     * After to Html
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Address $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(\Magento\Sales\Block\Adminhtml\Order\Create\Billing\Address $subject, $result)
    {
        $defaultVATInvoice = [];
        try {
            $customer = $this->_customerRepository->getById($subject->getCustomerId());
            if ($customer->getId()) {
                if ($customer->getCustomAttribute('default_vat_invoice')) {
                    $defaultVATInvoice = \Zend_Json::decode($customer->getCustomAttribute('default_vat_invoice')->getValue());
                }

                $order = $this->_getSession()->getOrder();
                $defaultVATInvoice['telephone_customer_consign'] = $order->getTelephoneCustomerConsign();
                $defaultVATInvoice['require'] = false;
                $defaultVATInvoice['checked'] = false;

                if (isset($defaultVATInvoice['company_name'])) {
                    $defaultVATInvoice['require'] = true;
                    $defaultVATInvoice['checked'] = true;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        $extraInformationOrderBlock = $subject->getLayout()->createBlock(
            Template::class,
            'vatinvoice.order.info',
            [
                'data' => [
                    'template' => 'Magenest_OrderExtraInformation::sales/order/create.phtml'
                ]
            ]
        )->setDefaultVATInvoice($defaultVATInvoice)->setTitleHeader(__('Extra Information'))->toHtml();

        return $result . $extraInformationOrderBlock;
    }

    /**
     * Retrieve session object
     *
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->_objectManager->get(\Magento\Backend\Model\Session\Quote::class);
    }
}
