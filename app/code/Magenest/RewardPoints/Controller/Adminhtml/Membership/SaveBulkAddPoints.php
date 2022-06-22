<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 19/11/2020 11:20
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magenest\RewardPoints\Model\ResourceModel\Transaction as TransactionResource;
use Magenest\RewardPoints\Model\TransactionFactory as TransactionModel;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class SaveBulkAddPoints extends \Magenest\RewardPoints\Controller\Adminhtml\Transaction\Save
{
    /**
     * @var \Magento\Backend\Model\Session\Proxy
     */
    private $backendSession;

    /**
     * SaveBulkAddPoints constructor.
     * @param TransactionResource $transactionResource
     * @param \Magenest\RewardPoints\Helper\Data $help
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param TransactionModel $transactionFactory
     * @param \Magento\Backend\Model\SessionFactory $sessionFactory
     * @param $session
     */
    public function __construct(
        TransactionResource $transactionResource, \Magenest\RewardPoints\Helper\Data $help,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magenest\RewardPoints\Model\TransactionFactory $transactionFactory,
        \Magento\Backend\Model\SessionFactory $sessionFactory,
        \Magento\Backend\Model\Session\Proxy $session
    ) {
        parent::__construct($transactionResource, $help, $resultJsonFactory, $context, $authSession, $transactionFactory, $sessionFactory);
        $this->backendSession = $session;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $listCustomerId = $this->backendSession->getAddPointsCustomers();

            $pointData = $this->getRequest()->getParams();

            $numCustomerAdded = 0;
            if (!empty($pointData) && !empty($listCustomerId)) {
                foreach ($listCustomerId as $customerId) {
                    $numCustomerAdded += $this->addPointToCustomer($customerId, $pointData['pointValue'], $pointData['pointDescription']) ? 1 : 0;
                }
            }
            $this->messageManager->addSuccessMessage(__('There are %1 customer(s) has been added points', $numCustomerAdded));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong while adding points for customer.'));
        }

        return $resultRedirect->setPath('customer/index');
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_RewardPoints::system_rewardpoints_membership');
    }
}
