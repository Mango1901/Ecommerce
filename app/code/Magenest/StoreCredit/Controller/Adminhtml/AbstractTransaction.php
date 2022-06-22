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

namespace Magenest\StoreCredit\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\StoreCredit\Helper\Data as HelperData;
use Magenest\StoreCredit\Model\Transaction;
use Magenest\StoreCredit\Model\TransactionFactory;

/**
 * Class AbstractTransaction
 * @package Magenest\StoreCredit\Controller\Adminhtml
 */
abstract class AbstractTransaction extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magenest_StoreCredit::transaction';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * AbstractTransaction constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Filter $filter
     * @param HelperData $helperData
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        HelperData $helperData,
        TransactionFactory $transactionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->filter = $filter;
        $this->helperData = $helperData;
        $this->transactionFactory = $transactionFactory;

        parent::__construct($context);
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return Page
     */
    protected function _initAction()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);

        return $resultPage;
    }

    /**
     * Initialize transaction object
     *
     * @return Transaction|null
     */
    protected function _initTransaction()
    {
        $transaction = $this->transactionFactory->create();

        if ($transactionId = $this->getRequest()->getParam('id', 0)) {
            $transaction->load($transactionId);
        }

        $this->registry->register('transaction', $transaction);

        return $transaction;
    }
}
