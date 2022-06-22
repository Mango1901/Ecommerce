<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 09/02/2021
 * Time: 14:15
 */

namespace Magenest\OrderSource\Block\Adminhtml\Order\Create\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\AdminOrder\Create;

/**
 * Class Account
 * @package Magenest\OrderSource\Block\Adminhtml\Order\Create\Form
 */
class Account extends \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account
{
    protected $serializer;

    /**
     * Account constructor.
     * @param SerializerInterface $serializer
     * @param Context $context
     * @param Quote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param FormFactory $metadataFormFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param array $data
     * @param GroupManagementInterface|null $groupManagement
     */
    public function __construct(
        SerializerInterface $serializer,
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Data\FormFactory $formFactory,
        DataObjectProcessor $dataObjectProcessor,
        FormFactory $metadataFormFactory,
        CustomerRepositoryInterface $customerRepository,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        array $data = [],
        ?GroupManagementInterface $groupManagement = null
    ) {
        $this->serializer = $serializer;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $formFactory,
            $dataObjectProcessor,
            $metadataFormFactory,
            $customerRepository,
            $extensibleDataObjectConverter,
            $data,
            $groupManagement
        );
    }

    public function getOrderSourceOptions()
    {
        try {
            return $this->serializer->unserialize($this->_scopeConfig->getValue('order_source/general/options'));
        } catch (\Exception $exception) {
            return [];
        }
    }
}
