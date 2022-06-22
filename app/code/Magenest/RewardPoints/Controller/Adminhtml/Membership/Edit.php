<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 29/10/2020 17:11
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory as MembershipCollection;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Membership
{
    /**
     * @var MembershipCollection
     */
    protected $_membershipCollection;

    /**
     * Edit constructor.
     * @param MembershipCollection $membershipCollection
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        MembershipCollection $membershipCollection,
        PageFactory $pageFactory,
        Action\Context $context
    ) {
        parent::__construct($pageFactory, $context);
        $this->_membershipCollection = $membershipCollection;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $membershipId = $this->getRequest()->getParam('id', 0);

        if ($membershipId) {
            $membership = $this->_membershipCollection->create()->addFieldToFilter(MembershipInterface::ENTITY_ID, $membershipId);
            if (!$membership->getSize()) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $this->messageManager->addError(__('This membership group doesn\'t exist!'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()
            ->prepend($membershipId ? __('Edit Group \'%1\' (ID: %2)', $membership->getFirstItem()->getData('name'), $membership->getFirstItem()->getId()) : __('New Membership Group'));

        return $resultPage;
    }
}