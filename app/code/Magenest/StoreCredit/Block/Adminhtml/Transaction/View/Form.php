<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Block\Adminhtml\Transaction\View;

use IntlDateFormatter;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magenest\StoreCredit\Helper\Data;
use Magenest\StoreCredit\Model\Transaction;

/**
 * Class Form
 * @package Magenest\StoreCredit\Block\Adminhtml\Transaction\View
 */
class Form extends Generic
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $transaction = $this->getTransaction();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save'),
                'method' => 'post',
            ]
        ]);

        $form->setFieldNameSuffix('transaction');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Transaction Information')]);

        if ($transaction->getId()) {
            $fieldset->addField('title', 'note', [
                'label' => __('Title'),
                'text' => $transaction->getTitle()
            ]);

            $fieldset->addField('status', 'note', [
                'label' => __('Status'),
                'text' => $transaction->getStatusLabel()
            ]);

            $customerId = $transaction->getCustomerId();
            $customer = $this->helper->getAccountHelper()->getCustomerById($customerId);
            $url = $this->getUrl('customer/index/edit', ['id' => $customerId]);
            $fieldset->addField('customer_email', 'note', [
                'label' => __('Customer'),
                'text' => '<a target="_blank" href="' . $url . '">' . $customer->getName() . ' &lt;' . $customer->getEmail() . '&gt;</a>'
            ]);

            $amount = $this->helper->formatPrice(
                $transaction->getAmount(),
                true,
                true,
                $customer->getStore()->getBaseCurrency()
            );
            $fieldset->addField('amount', 'note', [
                'label' => __('Amount'),
                'text' => '<strong>' . $amount . '</strong>',
            ]);

            $balance = $this->helper->formatPrice(
                $transaction->getBalance(),
                true,
                true,
                $customer->getStore()->getBaseCurrency()
            );
            $fieldset->addField('balance', 'note', [
                'label' => __('Balance'),
                'text' => '<strong>' . $balance . '</strong>',
            ]);

            if ($customerNote = $transaction->getCustomerNote()) {
                $fieldset->addField('customer_note', 'note', [
                    'label' => __('Customer Note'),
                    'text' => $customerNote
                ]);
            }

            if ($adminNote = $transaction->getAdminNote()) {
                $fieldset->addField('admin_note', 'note', [
                    'label' => __('Admin Note'),
                    'text' => $adminNote
                ]);
            }

            $fieldset->addField('created_at', 'note', [
                'label' => __('Created At'),
                'text' => $this->formatDate($transaction->getCreatedAt(), IntlDateFormatter::MEDIUM, true)
            ]);
        } else {
            $fieldset->addField('customer_id_form', 'hidden', [
                'name' => 'customer_id_form',
                'label' => __('Customer Id'),
                'title' => __('Customer Id'),
            ]);

            $fieldset->addField('customer_email', 'text', [
                'name' => 'customer_email',
                'label' => __('Customer'),
                'title' => __('Customer'),
                'required' => true,
                'readonly' => true,
                'text' => 'abcd'
            ])->setAfterElementHtml(
                '<div id="customer-grid" style="display:none"></div>
                <script type="text/x-magento-init">
                    {
                        "#customer_email": {
                            "Magenest_StoreCredit/js/view/transaction":{
                                "url": "' . $this->getAjaxUrl() . '"
                            }
                        }
                    }
                </script>'
            );

            $fieldset->addField('amount', 'text', [
                'name' => 'amount',
                'label' => __('Amount'),
                'title' => __('Amount'),
                'class' => 'validate-number',
                'required' => true
            ]);

            $fieldset->addField('customer_note', 'textarea', [
                'name' => 'customer_note',
                'label' => __('Customer Note'),
                'title' => __('Customer Note'),
                'note' => __('This note will be visible to customers. It will not be translated, please be conscious of the language.')
            ]);

            $fieldset->addField('admin_note', 'textarea', [
                'name' => 'admin_note',
                'label' => __('Admin Note'),
                'title' => __('Admin Note'),
                'note' => __('This note is visible to admin only.')
            ]);
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get transaction grid url
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('mpstorecredit/transaction/customergrid');
    }

    /**
     * Get Transaction
     *
     * @return Transaction|null
     */
    public function getTransaction()
    {
        return $this->_coreRegistry->registry('transaction');
    }
}
