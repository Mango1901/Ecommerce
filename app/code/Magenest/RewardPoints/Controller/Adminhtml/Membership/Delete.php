<?php
declare(strict_types=1);

/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Booking & Reservation extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 21/01/2021 17:42
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magenest\RewardPoints\Helper\MembershipAction;
use Magenest\RewardPoints\Model\MembershipFactory as MembershipModel;
use Magenest\RewardPoints\Model\ResourceModel\Membership as MembershipResource;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Membership
{
    /**
     * @var MembershipModel
     */
    protected $_membershipAction;

    /**
     * Delete constructor.
     * @param MembershipModel $membershipModel
     * @param MembershipResource $membershipResource
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        MembershipAction $membershipAction,
        PageFactory $pageFactory,
        Action\Context $context
    ) {
        parent::__construct($pageFactory, $context);
        $this->_membershipAction = $membershipAction;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $membershipId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($membershipId) {
            try {
                $this->_membershipAction->deleteMembershipGroup($membershipId);
                $this->messageManager->addSuccess(__('The group has been successfully deleted.'));
                $this->_getSession()->setPageData(false);

            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $membershipId]);
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}