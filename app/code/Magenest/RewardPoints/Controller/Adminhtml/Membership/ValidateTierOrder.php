<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 10/11/2020 13:19
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory as MembershipCollection;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class ValidateTierOrder extends Membership
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
        Action\Context $context
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
            'error' => 1,
            'result' => false
        ];
        try {
            $tier = $this->_membershipCollection->create()->addFieldToFilter(MembershipInterface::SORT_ORDER, $this->getRequest()->getParam('value'));
            if (!empty($this->getRequest()->getParam('membershipId'))) {
                $tier->addFieldToFilter(MembershipInterface::ENTITY_ID, ['neq' => $this->getRequest()->getParam('membershipId')]);
            }

            if ($tier->getSize() == 0) {
                $response['error'] = 0;
                $response['result'] = true;
            }
        } catch (\Exception $exception) {
            $response = [
                'error' => 0,
                'result' => false
            ];
        } finally {
            return $this->getResponse()->representJson(
                $this->_jsonHelper->jsonEncode($response)
            );
        }
    }
}