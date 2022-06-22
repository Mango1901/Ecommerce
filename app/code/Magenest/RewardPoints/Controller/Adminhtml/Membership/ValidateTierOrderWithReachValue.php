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
 * @time: 21/01/2021 10:39
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory as MembershipCollection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class ValidateTierOrderWithReachValue extends Membership
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var MembershipCollection
     */
    protected $_membershipCollection;

    /**
     * ValidateTierOrder constructor.
     * @param MembershipCollection $membershipCollection
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        MembershipCollection $membershipCollection,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        PageFactory $pageFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($pageFactory, $context);
        $this->_jsonHelper = $jsonHelper;
        $this->_membershipCollection = $membershipCollection;
    }
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $response = [
            'error' => 0,
            'result' => false
        ];
        try {
            $tier = $this->_membershipCollection->create()
                ->addFieldToFilter(MembershipInterface::SORT_ORDER, ['gt' => $this->getRequest()->getParam('sort_order')])
                ->addFieldToFilter(MembershipInterface::GROUP_CONDITION_TYPE, $this->getRequest()->getParam('condition_reach_tier'))
                ->addFieldToFilter(MembershipInterface::GROUP_CONDITION_VALUE, ['gteq' => $this->getRequest()->getParam('condition_reach_tier_value')]);
            if (!empty($this->getRequest()->getParam('membershipId'))) {
                $tier->addFieldToFilter(MembershipInterface::ENTITY_ID, ['neq' => $this->getRequest()->getParam('membershipId')]);
            }

            if ($tier->getSize() == 0) {
                $response['error'] = 0;
                $response['result'] = true;
            }
        } catch (\Exception $exception) {
            $response = [
                'error' => 1,
                'result' => false
            ];
        } finally {
            return $this->getResponse()->representJson(
                $this->_jsonHelper->jsonEncode($response)
            );
        }
    }
}