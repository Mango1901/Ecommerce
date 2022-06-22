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

namespace Magenest\StoreCredit\Block\Account\Dashboard;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magenest\StoreCredit\Block\Account\Dashboard;
use Magenest\StoreCredit\Helper\Data;
use Magenest\StoreCredit\Model\ResourceModel\Transaction\Collection;
use Magenest\StoreCredit\Model\ResourceModel\Transaction\CollectionFactory;

/**
 * Class Transaction
 * @method Collection getTransactions()
 * @method void setTransactions($transactions)
 * @package Magenest\StoreCredit\Block\Account\Dashboard
 */
class Transaction extends Dashboard
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Transaction constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $helper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $transactions = $this->collectionFactory->create()
            ->addFieldToFilter('customer_id', $this->helper->getAccountHelper()->getCustomerSession()->getCustomerId())
            ->setOrder('created_at', 'desc');

        $this->setTransactions($transactions);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getTransactions()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'mpstorecredit.transactions.pager'
            )
                ->setCollection($this->getTransactions());
            $this->setChild('pager', $pager);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
