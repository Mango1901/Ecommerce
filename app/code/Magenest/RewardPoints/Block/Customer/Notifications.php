<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 09/11/2020 14:09
 */

namespace Magenest\RewardPoints\Block\Customer;

use Magenest\RewardPoints\Api\Data\NotificationInterface;
use Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;
use Magento\Customer\Model\Session;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

class Notifications extends Template
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var RuleCollection
     */
    protected $_ruleCollection;

    /**
     * @var Json
     */
    protected $_json;

    /**
     * Notifications constructor.
     * @param Json $json
     * @param RuleCollection $ruleCollection
     * @param Session $customerSession
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Json $json,
        RuleCollection $ruleCollection,
        Session $customerSession,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
        $this->_ruleCollection = $ruleCollection;
        $this->_json = $json;
    }

    /**
     * @param int $area
     * @return \Magento\Framework\DataObject[]
     */
    public function getListNotifications($area = NotificationInterface::NOTIFICATION_DISPLAY_POSITION_CUSTOMER)
    {
        $notificationsDisabled = empty($this->_customerSession->getNotificationsDisabled()) ? [] : $this->_json->unserialize($this->_customerSession->getNotificationsDisabled());
        $notifications = $this->_ruleCollection->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter(NotificationInterface::NOTIFICATION_STATUS, NotificationInterface::NOTIFICATION_STATUS_ENABLE)
            ->addFieldToFilter(NotificationInterface::NOTIFICATION_DISPLAY_POSITION, $area);

        if (!empty($notificationsDisabled)) {
            $notifications->addFieldToFilter(NotificationInterface::ENTITY_ID, ['nin' => $notificationsDisabled]);
        }

        // show notification for guest
        if (empty($this->_customerSession->getCustomerId())) {
            $notifications->addFieldToFilter(NotificationInterface::NOTIFICATION_DISPLAY_FOR_GUEST, NotificationInterface::NOTIFICATION_DISPLAY_FOR_GUEST_YES);
        }

        return $notifications->getItems();
    }

    /**
     * @param $notificationId
     * @return string
     */
    public function getLinkDisableNotification($notificationId)
    {
        return $this->getUrl('rewardpoints/customer/disableNotifications', ['notification_id' => $notificationId]);
    }
}