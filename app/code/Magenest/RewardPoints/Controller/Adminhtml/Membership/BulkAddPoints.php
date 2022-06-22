<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 18/11/2020 14:54
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magento\Backend\App\Action;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

class BulkAddPoints extends Membership
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var \Magento\Backend\Model\Session\Proxy
     */
    private $backendSession;

    /**
     * BulkAddPoints constructor.
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     * @param $session
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Filter $filter,
        PageFactory $pageFactory,
        Action\Context $context,
        \Magento\Backend\Model\Session\Proxy $session
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->backendSession = $session;
        parent::__construct($pageFactory, $context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->_collectionFactory->create());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }

        $this->backendSession->setAddPointsCustomers($collection->getAllIds());
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Bulk Add Points'));

        return $resultPage;
    }
}
