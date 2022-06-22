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
 * @time: 21/01/2021 17:09
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magenest\RewardPoints\Helper\MembershipAction;
use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory as MembershipCollection;
use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Membership
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var MembershipCollection
     */
    protected $_membershipCollection;

    /**
     * @var MembershipAction
     */
    protected $_membershipAction;

    /**
     * MassDelete constructor.
     * @param MembershipAction $membershipAction
     * @param MembershipCollection $membershipCollection
     * @param Filter $filter
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        MembershipAction $membershipAction,
        MembershipCollection $membershipCollection,
        Filter $filter,
        PageFactory $pageFactory,
        Action\Context $context
    ) {
        parent::__construct($pageFactory, $context);
        $this->_filter = $filter;
        $this->_membershipCollection = $membershipCollection;
        $this->_membershipAction = $membershipAction;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_membershipCollection->create());
        $membershipDeleted = $this->_membershipAction->deleteMembershipGroup($collection->getAllIds());
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $membershipDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('rewardpoints/*/index');
    }
}