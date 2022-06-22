<?php


namespace Magenest\Affiliate\Block\Adminhtml\Withdraw\Edit\Tab;

use Magenest\Affiliate\Block\Adminhtml\Customer\Grid;
use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magenest\Affiliate\Helper\Data as HelperData;
use Magenest\Affiliate\Helper\Payment;
use Magenest\Affiliate\Model\Withdraw\Method;
use Magenest\Affiliate\Model\Withdraw\Status;
use Zend_Serializer_Exception;

/**
 * Class Withdraw
 * @package Magenest\Affiliate\Block\Adminhtml\Withdraw\Create\Tab
 */
class Withdraw extends Generic implements TabInterface
{
    /**
     * @type CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Method
     */
    protected $_method;

    /**
     * @var Payment
     */
    protected $_paymentHelper;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Withdraw constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CustomerFactory $customerFactory
     * @param Method $method
     * @param Status $status
     * @param Payment $payment
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context         $context,
        Registry        $registry,
        FormFactory     $formFactory,
        CustomerFactory $customerFactory,
        Method          $method,
        Status          $status,
        Payment         $payment,
        HelperData      $helperData,
        array           $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->_method         = $method;
        $this->_paymentHelper  = $payment;
        $this->_status         = $status;
        $this->helperData      = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magenest\Affiliate\Model\Withdraw $withdraw */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('withdraw_');
        $form->setFieldNameSuffix('withdraw');
        $withdraw = $this->_coreRegistry->registry('current_withdraw');
        if ($withdraw->getId()) {
            $this->viewWithdraw($form);
        } else {
            $this->createWithdraw($form);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function prepareWithdrawData()
    {
        $withdraw = $this->_coreRegistry->registry('current_withdraw');
        $customer = $this->customerFactory->create()->load($withdraw->getCustomerId());
        $withdraw->setCustomerName($customer->getName() . ' <' . $customer->getEmail() . '>');

        return $withdraw->getData();
    }

    /**
     * @param $form
     *
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function createWithdraw($form)
    {
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Withdraw Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        $fieldset->addField(
            'customer_id',
            'hidden',
            [
                'name' => 'customer_id'
            ]
        );

        $this->helperData->addCustomerEmailFieldset(
            $fieldset,
            'withdraw',
            $this->getAjaxUrl(),
            Grid::CREATE_WITHDRAW_ACTION
        );

        $fieldset->addField('current_balance', 'note', [
            'label' => __('Current Balance'),
            'text'  => $this->helperData->formatPrice(0)
        ]);
        if ($payment = $this->helperData->getPaymentMethod()) {
            $paymentData = $this->helperData->unserialize($payment);
            $feeConfig = $paymentData[PaymentAttributeInterface::CODE_VNPT_EPAY]['fee'] ?? null;
            if ($feeConfig) {
                if (strpos($feeConfig, '%') === false) {
                    $feeConfig = $this->helperData->formatPrice($feeConfig);
                }
            }

        }
        $fieldset->addField(
            'amount',
            'text',
            [
                'label'    => __('Amount'),
                'name'     => 'amount',
                'required' => true,
                'class'    => 'validate-number',
                'note'     => __('Real amount will be transfer to affiliate. Excluding fee %1.', $feeConfig ?? false)
            ]
        );
        $fieldset->addField(
            'fee',
            'text',
            [
                'label' => __('Fee'),
                'name'  => 'fee',
                'class' => 'validate-number',
                'note'  => __(
                    'If empty, configuration value will be used. The actual amount transferred to the customer must not be less than 10000.'
                )
            ]
        );
    }

    /**
     * @param $form
     */
    public function viewWithdraw($form)
    {
        $fieldset      = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('If transaction is pending, you can complete by massaction on grid.'),
                'class'  => 'fieldset-wide'
            ]
        );
        $transactionId = $this->getRequest()->getParam('id');
        if ($transactionId) {
            $fieldset->addField(
                'edit_record',
                'hidden',
                [
                    'name'  => 'edit_record',
                    'value' => true
                ]
            );
        }

        $fieldset->addField(
            'customer_id',
            'hidden',
            [
                'name' => 'customer_id'
            ]
        );
        $fieldset->addField(
            'account_id',
            'hidden',
            [
                'name' => 'customer_id'
            ]
        );
        $fieldset->addField(
            'customer_name',
            'text',
            [
                'label'    => __('Account'),
                'name'     => 'customer_name',
                'readonly' => true
            ]
        );
        $fieldset->addField(
            'amount',
            'text',
            [
                'label'    => __('Amount'),
                'name'     => 'amount',
                'readonly' => true,
                'class'    => 'validate-number',
                'note'     => __('Real amount will be transfer to affiliate. Excluding fee.')
            ]
        );
        $fieldset->addField(
            'fee',
            'text',
            [
                'label'    => __('Fee'),
                'name'     => 'fee',
                'readonly' => true,
                'class'    => 'validate-number',
                'note'     => __(
                    'If empty, configuration value will be used. The actual amount transferred to the customer must not be less than 10000.'
                )
            ]
        );
        $fieldset->addField(
            'transfer_amount',
            'text',
            [
                'label'    => __('Transfer Amount'),
                'name'     => 'transfer_amount',
                'readonly' => true,
                'class'    => 'validate-number',
                'note'     => __('Amount will be deducted from customer balance.')
            ]
        );
        $withdraw = $this->_coreRegistry->registry('current_withdraw');

        $fieldset->addField(
            'bank_no',
            'note',
            [
                'label' => __("Bank Type"),
                'text'  => $withdraw->getBankNo(),
            ]
        );
        $fieldset->addField(
            'acc_no',
            'note',
            [
                'label' => $withdraw->getAccType() ? __("Card Number") : __("Bank Number"),
                'text'  => $withdraw->getAccNo(),
            ]
        );
        $fieldset->addField(
            'account_name',
            'note',
            [
                'label' => __("Bank Owner"),
                'text'  => $withdraw->getAccountName(),
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name'     => 'status',
                'label'    => __('Status'),
                'title'    => __('Status'),
                'readonly' => true,
                'disabled' => true,
                'values'   => $this->_status->toOptionArray()
            ]
        );

        $form->addValues($this->prepareWithdrawData());
    }

    /**
     * Prepare label for tab
     * @return string
     */
    public function getTabLabel()
    {
        return __('Withdraw');
    }

    /**
     * Prepare title for tab
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getCurrentWithdraw()
    {
        return $this->_coreRegistry->registry('current_withdraw');
    }


    /**
     * Get transaction grid url
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('affiliate/customer/grid', ['action' => Grid::CREATE_WITHDRAW_ACTION]);
    }
}
