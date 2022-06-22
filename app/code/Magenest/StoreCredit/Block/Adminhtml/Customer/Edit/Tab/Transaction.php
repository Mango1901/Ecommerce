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

namespace Magenest\StoreCredit\Block\Adminhtml\Customer\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Magenest\StoreCredit\Helper\Data as DataHelper;
use Magenest\StoreCredit\Model\Config\Source\Action;
use Magenest\StoreCredit\Model\Config\Source\Status;
use Magenest\StoreCredit\Model\ResourceModel\Transaction\CollectionFactory;

/**
 * Class Transaction
 * @package Magenest\StoreCredit\Block\Adminhtml\Customer\Edit\Tab
 */
class Transaction extends Extended
{
    /**
     * @type CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Action
     */
    protected $_action;

    /**
     * @type DataHelper
     */
    protected $_helper;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Transaction constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param Action $action
     * @param Registry $registry
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Action $action,
        Registry $registry,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_action = $action;
        $this->_coreRegistry = $registry;
        $this->_helper = $dataHelper;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('transaction_grid');
        $this->setDefaultSort('transaction_id', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Get Customer Id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create()->addFieldToFilter('customer_id', $this->getCustomerId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this|Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
            'header' => __('ID'),
            'index' => 'transaction_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);

        $this->addColumn('title', [
            'header' => __('Title'),
            'index' => 'title',
            'type' => 'text'
        ]);

        $this->addColumn('status', [
            'header' => __('Status'),
            'filter' => false,
            'index' => 'status',
            'type' => 'options',
            'options' => Status::getOptionArray()
        ]);

        $this->addColumn('action', [
            'header' => __('Action'),
            'index' => 'action',
            'type' => 'options',
            'options' => $this->_action->getOptionArray()
        ]);

        $customer = $this->_helper->getAccountHelper()->getCustomerById($this->getCustomerId());

        $this->addColumn('amount', [
            'header' => __('Amount'),
            'filter' => false,
            'align' => 'right',
            'index' => 'amount',
            'type' => 'price',
            'currency_code' => $customer->getStore()->getBaseCurrencyCode()
        ]);

        $this->addColumn('balance', [
            'header' => __('Balance'),
            'filter' => false,
            'align' => 'right',
            'index' => 'balance',
            'type' => 'price',
            'currency_code' => $customer->getStore()->getBaseCurrencyCode()
        ]);

        $this->addColumn('created_at', [
            'header' => __('Date'),
            'type' => 'datetime',
            'index' => 'created_at',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date'
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('mpstorecredit/customer/grid', ['_current' => true]);
    }
}
