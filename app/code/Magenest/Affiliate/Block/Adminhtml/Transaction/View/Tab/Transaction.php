<?php


namespace Magenest\Affiliate\Block\Adminhtml\Transaction\View\Tab;

use IntlDateFormatter;
use Magenest\Affiliate\Block\Adminhtml\Customer\Grid;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\User\Model\UserFactory;
use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\Transaction\Status;
use Magenest\Affiliate\Model\Transaction\Type;

/**
 * Class Transaction
 *
 * @package Magenest\Affiliate\Block\Adminhtml\Transaction\View\Tab
 */
class Transaction extends Generic implements TabInterface
{
    /**
     * @var Type
     */
    protected $_transactionType;

    /**
     * @var Status
     */
    protected $_transactionStatus;

    /**
     * @type CustomerFactory
     */
    protected $customerFactory;

    /**
     * @type JsonHelper
     */
    protected $jsonHelper;

    /**
     * @type UserFactory
     */
    protected $user;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Transaction constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CustomerFactory $customerFactory
     * @param Type $type
     * @param Status $status
     * @param JsonHelper $jsonHelper
     * @param UserFactory $userFactory
     * @param Data $helperData
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CustomerFactory $customerFactory,
        Type $type,
        Status $status,
        JsonHelper $jsonHelper,
        UserFactory $userFactory,
        Data $helperData,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->jsonHelper         = $jsonHelper;
        $this->user               = $userFactory;
        $this->customerFactory    = $customerFactory;
        $this->_transactionType   = $type;
        $this->_transactionStatus = $status;
        $this->helperData         = $helperData;
        $this->priceCurrency      = $priceCurrency;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magenest\Affiliate\Model\Transaction $transaction */
        $transaction = $this->_coreRegistry->registry('current_transaction');
        $form        = $this->_formFactory->create();
        $form->setHtmlIdPrefix('transaction_');
        $form->setFieldNameSuffix('transaction');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Transaction Information'),
            'class'  => 'fieldset-wide'
        ]);

        if ($transaction->getId()) {
            $this->editTransaction($fieldset, $transaction);
        } else {
            $this->addTransaction($fieldset);
        }

        $form->addValues($transaction->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param $fieldset
     * @param $transaction
     *
     * @throws LocalizedException
     */
    public function editTransaction($fieldset, $transaction)
    {
        $customerModel = $this->customerFactory->create()->load($transaction->getCustomerId());
        $fieldset->addField('affiliate_account', 'link', [
            'href'   => $this->getUrl('affiliate/account/edit', ['id' => $transaction->getAccountId()]),
            'name'   => 'affiliate_account',
            'value'  => $customerModel->getName() . ' <' . $this->escapeHtml($customerModel->getEmail()) . '>',
            'label'  => __('Affiliate Account'),
            'target' => '_blank',
            'style'  => 'text-decoration: none',
            'class'  => 'control-value'
        ]);

        $transactionType = $this->_transactionType->getOptionHash();
        $fieldset->addField('type', 'note', [
            'label' => __('Type'),
            'text'  => $transactionType[$transaction->getType()]
        ]);
        $fieldset->addField('title', 'note', [
            'label' => __('Title'),
            'text'  => $transaction->getTitle()
        ]);
        $fieldset->addField('amount', 'note', [
            'label' => __('Amount'),
            'text'  => $this->priceCurrency->format(
                $transaction->getAmount(),
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                null,
                $customerModel->getStore()->getBaseCurrencyCode()
            )
        ]);
        $transactionStatus = $this->_transactionStatus->getOptionHash();
        $fieldset->addField('status', 'note', [
            'label' => __('Status'),
            'text'  => $transactionStatus[$transaction->getStatus()]
        ]);

        if ($transaction->getOrderId()) {
            $fieldset->addField('order', 'link', [
                'href'   => $this->getUrl('sales/order/view', ['order_id' => $transaction->getOrderId()]),
                'name'   => 'order',
                'value'  => '#' . $transaction->getOrderIncrementId(),
                'label'  => __('Order'),
                'target' => '_blank',
                'style'  => 'text-decoration: none',
                'class'  => 'control-value'
            ]);
        }

        if ($transaction->getExtraContent() && ($transaction->getType() == Type::ADMIN)) {
            $extraContent = $this->jsonHelper->jsonDecode($transaction->getExtraContent());
            if (is_array($extraContent) && isset($extraContent['admin_id'])) {
                $admin = $this->user->create()->load($extraContent['admin_id']);
                $href  = $this->getUrl('adminhtml/user/edit', ['user_id' => $admin->getId()]);
                $fieldset->addField('admin_account', 'link', [
                    'label'  => __('Created by'),
                    'href'   => $href,
                    'value'  => $admin->getName() . ' <' . $this->escapeHtml($admin->getEmail()) . '>',
                    'target' => '_blank',
                    'style'  => 'text-decoration: none',
                    'class'  => 'control-value'
                ]);
            }
        }

        if ($transaction->getStatus() == Status::STATUS_HOLD && $transaction->getHoldingTo()) {
            $fieldset->addField('holding_to', 'note', [
                'label' => __('This transaction will be holed to'),
                'text'  => $this->_localeDate->formatDate(
                    $transaction->getHoldingTo(),
                    IntlDateFormatter::MEDIUM,
                    true
                )
            ]);
        }

        $fieldset->addField('created_at', 'note', [
            'label' => __('Created Time'),
            'text'  => $this->_localeDate->formatDate(
                $transaction->getCreatedAt(),
                IntlDateFormatter::MEDIUM,
                true
            )
        ]);
    }

    /**
     * @param $fieldset
     */
    public function addTransaction($fieldset)
    {
        $fieldset->addField('customer_id', 'hidden', [
            'name' => 'customer_id'
        ]);

        $this->helperData->addCustomerEmailFieldset(
            $fieldset,
            'transaction',
            $this->getAjaxUrl(),
            Grid::CREATE_TRANSACTION_ACTION
        );

        $fieldset->addField('amount', 'text', [
            'label'    => __('Amount'),
            'name'     => 'amount',
            'required' => true,
            'class'    => 'validate-number',
            'note'     => __('Add or subtract affiliate\'s balance. E.g: 99 or -99')
        ]);
        $fieldset->addField('title', 'text', [
            'label' => __('Title'),
            'name'  => 'title'
        ]);
        $fieldset->addField('hold_day', 'text', [
            'name'  => 'hold_day',
            'label' => __('Holding Transaction For'),
            'note'  => 'day(s)'
        ]);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Transaction');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get transaction grid url
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('affiliate/customer/grid', ['action' => Grid::CREATE_TRANSACTION_ACTION]);
    }
}
