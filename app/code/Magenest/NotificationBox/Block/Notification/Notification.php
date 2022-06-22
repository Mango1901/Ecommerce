<?php

namespace Magenest\NotificationBox\Block\Notification;

use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magenest\NotificationBox\Model\NotificationTypeFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\CollectionFactory;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType;
use Magento\Customer\Model\Session;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Reports\Model\ResourceModel\Quote\Collection;
use Magento\Store\Model\StoreManagerInterface;

class Notification extends Template
{
    /** @var Json */
    protected $serialize;

    /** @var Session */
    protected $session;

    /** @var CustomerNotificationFactory */
    protected $customerNotificationFactory;

    /** @var CustomerNotification */
    protected $customerNotificationResource;

    /** @var UrlInterface */
    protected $urlInterface;

    /** @var CollectionFactory */
    protected $collectionFactory;

    /** @var Collection */
    protected $collection;

    /** @var Helper */
    protected $helper;

    /** @var null */
    protected $notificationCollection = null;

    /** @var StoreManagerInterface */
    protected $storeManagerInterface;

    /** @var NotificationTypeFactory */
    protected $notificationTypeFactory;

    /** @var NotificationType */
    protected $notificationTypeResource;

    /** @var TimezoneInterface */
    protected $timezoneInterface;

    /**
     * @param Collection $collection
     * @param Context $context
     * @param CustomerNotificationFactory $customerNotificationModel
     * @param CustomerNotification $customerNotificationResource
     * @param Session $session
     * @param CollectionFactory $collectionFactory
     * @param Json $serialize
     * @param Helper $helper
     * @param StoreManagerInterface $storeManagerInterface
     * @param NotificationTypeFactory $notificationTypeFactory
     * @param NotificationType $notificationTypeResource
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        Collection                  $collection,
        Context                     $context,
        CustomerNotificationFactory $customerNotificationModel,
        CustomerNotification        $customerNotificationResource,
        Session                     $session,
        CollectionFactory           $collectionFactory,
        Json                        $serialize,
        Helper                      $helper,
        StoreManagerInterface       $storeManagerInterface,
        NotificationTypeFactory     $notificationTypeFactory,
        NotificationType            $notificationTypeResource,
        TimezoneInterface           $timezoneInterface
    ) {
        parent::__construct($context);
        $this->collection = $collection;
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->customerNotificationFactory = $customerNotificationModel;
        $this->customerNotificationResource = $customerNotificationResource;
        $this->urlInterface = $context->getUrlBuilder();
        $this->serialize = $serialize;
        $this->helper = $helper;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->notificationTypeFactory = $notificationTypeFactory;
        $this->notificationTypeResource = $notificationTypeResource;
        $this->timezoneInterface = $timezoneInterface;
    }

    /** get all notification unread */
    public function getAllNotificationUnread()
    {
        return $this->getAllCustomerNotification()->addFieldToFilter('status', 0);
    }

    /** get all customer notification */
    public function getAllCustomerNotification()
    {
        if ($customerId = $this->getCustomerId()) {
            $this->notificationCollection = $this->collectionFactory->create()->addFieldToFilter('customer_id', $customerId);
        }
        return $this->notificationCollection;
    }

    /** check customer is login or not
     * return customer id
     */
    public function getCustomerId()
    {
        return $this->helper->getCustomerId();
    }

    /** get all notification */
    public function AllNotification()
    {
        $numberNotification = $this->helper->getMaximumNotificationOnNotificationBox();
        $allNotification = $this->getAllCustomerNotification()->setPageSize($numberNotification)->setOrder('entity_id', 'DESC')->getData();
        foreach ($allNotification as $key => $notification) {
            $notification['image'] = $this->helper->getImageByNotificationType($notification);
            $allNotification[$key]['icon'] = $notification['image'];
            if (isset($notification['description'])) {
                $maximumCharacter = $this->helper->getMaximumNotificationDescription();
                $allNotification[$key]['description'] = strlen($notification['description']) <= $maximumCharacter ? $notification['description'] : mb_substr($notification['description'], 0, $maximumCharacter, 'UTF-8') . "...";
            }
            if ($notification['redirect_url'] == null) {
                $allNotification[$key]['redirect_url'] = $this->getUrl('notibox/customer/notification');
                if ($notification['notification_type'] == \Magenest\NotificationBox\Model\Notification::ORDER_STATUS_UPDATE) {
                    $allNotification[$key]['redirect_url'] = $this->getUrl('sales/order/history/');
                }
                if ($notification['notification_type'] == \Magenest\NotificationBox\Model\Notification::REVIEW_REMINDERS) {
                    $allNotification[$key]['redirect_url'] = $this->getUrl('checkout/cart/');
                }
                if ($notification['notification_type'] == \Magenest\NotificationBox\Model\Notification::BIRTHDAY) {
                    $allNotification[$key]['redirect_url'] = '';
                }

                if ($notification['notification_type'] == \Magenest\NotificationBox\Model\Notification::AFFILIATE_PROGRAM) {
                    $allNotification[$key]['redirect_url'] = '';
                }

                if ($notification['notification_type'] == \Magenest\NotificationBox\Model\Notification::CUSTOMER_LOGIN) {
                    $allNotification[$key]['redirect_url'] = '';
                }

                if ($notification['notification_type'] == \Magenest\NotificationBox\Model\Notification::PRODUCT_WISHLIST_PROMOTIONS) {
                    $allNotification[$key]['redirect_url'] = $this->getUrl('wishlist/');
                }
            }
            if (isset($notification['created_at'])) {
                $allNotification[$key]['created_at'] = $this->timezoneInterface->formatDateTime($notification['created_at'], 2, 2);
            }
        }
        return $allNotification;
    }

    /** redirect to login page if customer is not login */
    public function redirectIfNotLoggedIn()
    {
        if (!$this->session->isLoggedIn()) {
            $this->session->setAfterAuthUrl($this->urlInterface->getCurrentUrl());
            $this->session->authenticate();
        }
    }

    /** get background color for notification box */
    public function getThemeColor()
    {
        return $this->helper->getThemeColor();
    }

    /** get all notification is unread */
    public function getUnreadNotification()
    {
        return $this->helper->getUnreadNotification();
    }

    /** get sender id */
    public function getSenderId()
    {
        return $this->helper->getSenderId();
    }

    /** get time resend popup */
    public function getTimeResendPopUp()
    {
        return $this->helper->getTimeResendPopUp();
    }

    public function getBoxPosition()
    {
        return $this->helper->getBoxPosition();
    }

    public function getBoxWidth()
    {
        return $this->helper->getBoxWidth();
    }
}
