<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 04/11/2020 14:03
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer\CollectionFactory as MembershipCustomerCollection;
use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magenest\RewardPoints\Helper\MembershipAction;
use Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer as MembershipCustomerResource;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;

class Save extends Membership
{
    const DEFAULT_PATH_IMAGE = \Magento\Framework\UrlInterface::URL_TYPE_MEDIA . '/rewardpoint/membership/';

    /**
     * @var \Magenest\RewardPoints\Model\MembershipFactory
     */
    protected $_membership;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Membership
     */
    protected $_membershipResource;

    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $imageUploader;

    /**
     * @var MembershipCustomerResource
     */
    protected $_membershipCustomerResource;

    /**
     * @var MembershipCustomerCollection
     */
    protected $_membershipCustomerCollection;

    /**
     * @var Json
     */
    protected $_jsonHelper;

    /**
     * @var MembershipAction
     */
    protected $_membershipAction;

    /**
     * Save constructor.
     * @param MembershipAction $membershipAction
     * @param Json $jsonhelper
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader
     * @param MembershipCustomerCollection $membershipCustomerCollection
     * @param MembershipCustomerResource $membershipCustomerResource
     * @param \Magenest\RewardPoints\Model\MembershipFactory $membership
     * @param \Magenest\RewardPoints\Model\ResourceModel\Membership $membershipResource
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        MembershipAction $membershipAction,
        Json $jsonhelper,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        MembershipCustomerCollection $membershipCustomerCollection,
        MembershipCustomerResource $membershipCustomerResource,
        \Magenest\RewardPoints\Model\MembershipFactory $membership,
        \Magenest\RewardPoints\Model\ResourceModel\Membership $membershipResource,
        PageFactory $pageFactory,
        Action\Context $context
    ) {
        parent::__construct($pageFactory, $context);
        $this->_membership = $membership;
        $this->_membershipResource = $membershipResource;
        $this->_membershipCustomerResource = $membershipCustomerResource;
        $this->imageUploader = $imageUploader;
        $this->_jsonHelper = $jsonhelper;
        $this->_membershipAction = $membershipAction;
        $this->_membershipCustomerCollection = $membershipCustomerCollection;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $postData = $this->getRequest()->getPostValue();
        try {
            $membership = $this->initMembership();

            $data = $postData['membership'];

            if (isset($data['tier_logo'][0]['name']) && isset($data['tier_logo'][0]['tmp_name'])) {
                // upload image
                $imageName = $data['tier_logo'][0]['name'];
                $data['tier_logo'][0]['url'] = self::DEFAULT_PATH_IMAGE . $data['tier_logo'][0]['name'];
                $this->imageUploader->moveFileFromTmp($imageName);
                unset($data['tier_logo'][0]['tmp_name']);
            }

            if (!empty($data['tier_logo'][0]['path'])) {
                $data['tier_logo'][0]['url'] = $data['tier_logo'][0]['path'];
            }
            $data['tier_logo'] = empty($data['tier_logo']) ? '' : $this->_jsonHelper->serialize($data['tier_logo']);
            if (!empty($data)) {
                $membership->setData($data);
                $this->_membershipResource->save($membership);
            }

            $newAssignedCustomerIds = $this->getNewAssignedCustomer($membership->getId());
            $this->saveMembershipCustomer($membership->getId());
            $this->sendEmailNotificationChangeTier($newAssignedCustomerIds, $membership);

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $membership->getId(), '_current' => true]);
            }

        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->_getSession()->setPageData($postData['membership']);
            return $resultRedirect->setPath('*/*/edit', ['id' => empty($postData['membership']['id']) ? null : $postData['membership']['id']]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $membershipId
     * @throws LocalizedException
     * @throws \Exception
     */
    public function saveMembershipCustomer($membershipId)
    {
        try {
            if (!empty($membershipId)) {
                if ($this->getRequest()->getParam('assigned_customer')) {
                    $assignedCustomers = $this->_jsonHelper->unserialize($this->getRequest()->getParam('assigned_customer'));
                    $membershipCustomerData = [];
                    foreach ($assignedCustomers as $customerId) {
                        $membershipCustomerData[] = [
                            MembershipCustomerInterface::MEMBERSHIP_ID => $membershipId,
                            MembershipCustomerInterface::CUSTOMER_ID => $customerId
                        ];
                    }
                    $this->_membershipCustomerResource->assignCustomerToTier($membershipId, $membershipCustomerData);
                }
            }
        } catch (\Magento\Framework\DB\Adapter\DuplicateException $duplicateException) {
            throw new \Exception(__('The customer(s) you just added belong to another group. Please remove the customer(s) from the current group first and try again!'));
        }
    }

    /**
     * @return \Magenest\RewardPoints\Model\Membership
     * @throws LocalizedException
     */
    protected function initMembership()
    {
        $membership = $this->_membership->create();
        $membershipId = $this->getRequest()->getParam('id', false);
        if ($membershipId) {
            $this->_membershipResource->load($membership, $membershipId);
            if ($membershipId != $membership->getId()) {
                throw new LocalizedException(__('Something went wrong, please try again later.'));
            }
        }

        return $membership;
    }

    /**
     * @param $customerIds
     * @param $membership
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendEmailNotificationChangeTier($customerIds, $membership)
    {
        foreach ($customerIds as $customerId) {
            $this->_membershipAction->setCustomerId($customerId)->sendEmailChangeTier($membership);
        }
    }

    /**
     * @param $membershipId
     * @return array|bool|float|int|mixed|string|null
     */
    public function getNewAssignedCustomer($membershipId)
    {
        if (!empty($membershipId)) {
            if ($this->getRequest()->getParam('assigned_customer')) {

                $newAssignedCustomers = $this->_jsonHelper->unserialize($this->getRequest()->getParam('assigned_customer'));

                $assignedCustomers = $this->_membershipCustomerCollection->create()
                    ->addFieldToFilter(MembershipCustomerInterface::MEMBERSHIP_ID, $membershipId)->getAllIds();

                return array_diff($newAssignedCustomers, $assignedCustomers);
            }
        }

        return [];
    }
}