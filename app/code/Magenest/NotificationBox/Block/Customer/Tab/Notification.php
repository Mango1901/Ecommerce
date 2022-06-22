<?php

namespace Magenest\NotificationBox\Block\Customer\Tab;

use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magenest\NotificationBox\Model\Notification as NotificationModel;
use Magenest\NotificationBox\Model\NotificationType as NotificationTypeModel;
use Magenest\NotificationBox\Model\NotificationTypeFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\CollectionFactory;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType\CollectionFactory as NotificationTypeCollection;
use Magento\Customer\Model\Session;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Reports\Model\ResourceModel\Quote\Collection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Notification
 */
class Notification extends Template
{
    const NOTIFICATION_BOX_DEFAULT = [
        NotificationModel::ORDER_STATUS_UPDATE,
        NotificationModel::ABANDONED_CART_REMINDS,
        NotificationModel::REVIEW_REMINDERS,
        NotificationModel::REWARD_POINT_REMINDS,
        NotificationModel::STORE_CREDIT_REMINDS,
        NotificationModel::BIRTHDAY,
        NotificationModel::AFFILIATE_PROGRAM,
        NotificationModel::NEWSLETTER,
        NotificationModel::PRODUCT_WISHLIST_PROMOTIONS,
        NotificationModel::CUSTOMER_LOGIN,
        NotificationModel::MAINTENANCE
    ];

    /** @var Json */
    protected $serialize;

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

    /** @var Session */
    protected $session;

    /** @var NotificationType */
    protected $notificationTypeResource;

    /** @var NotificationTypeFactory */
    protected $notificationTypeFactory;

    /** @var null */
    protected $notificationCollection = null;

    /** @var StoreManagerInterface */
    protected $storeManagerInterface;

    /** @var NotificationTypeCollection */
    protected $notificationTypeCollection;

    /**
     * @param Collection $collection
     * @param Context $context
     * @param Json $serialize
     * @param CustomerNotificationFactory $customerNotificationModel
     * @param CustomerNotification $customerNotificationResource
     * @param CollectionFactory $collectionFactory
     * @param Helper $helper
     * @param Session $session
     * @param NotificationType $notificationTypeResource
     * @param NotificationTypeFactory $notificationTypeFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param NotificationTypeCollection $notificationTypeCollection
     */
    public function __construct(
        Collection                  $collection,
        Context                     $context,
        Json                        $serialize,
        CustomerNotificationFactory $customerNotificationModel,
        CustomerNotification        $customerNotificationResource,
        CollectionFactory           $collectionFactory,
        Helper                      $helper,
        Session                     $session,
        NotificationType            $notificationTypeResource,
        NotificationTypeFactory     $notificationTypeFactory,
        StoreManagerInterface       $storeManagerInterface,
        NotificationTypeCollection  $notificationTypeCollection
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->helper = $helper;
        $this->collection = $collection;
        $this->serialize = $serialize;
        $this->collectionFactory = $collectionFactory;
        $this->customerNotificationFactory = $customerNotificationModel;
        $this->customerNotificationResource = $customerNotificationResource;
        $this->urlInterface = $context->getUrlBuilder();
        $this->notificationTypeFactory = $notificationTypeFactory;
        $this->notificationTypeResource = $notificationTypeResource;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->notificationTypeCollection = $notificationTypeCollection;
    }

    /**
     * @return mixed
     */
    public function isDeleteNotification()
    {
        return $this->helper->getAllowCustomerDeleteNotification();
    }

    /**
     * Check customer is login or not
     * If the customer is not logged in, redirect to the login page
     */
    public function redirectIfNotLoggedIn()
    {
        if (!$this->session->isLoggedIn()) {
            $this->session->setAfterAuthUrl($this->urlInterface->getCurrentUrl());
            $this->session->authenticate();
        }
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get unread notification color
     */
    public function getUnreadNotification()
    {
        return $this->helper->getUnreadNotification();
    }

    /**
     * @param $condition
     * @return array
     */
    public function getNotificationByCondition($condition)
    {
        if (isset($condition)) {
            $allNotification = $this->helper->getAllNotificationByCondition($condition)->getData();
        } else {
            $allNotification = $this->getAllCustomerNotification("all");
            if ($allNotification) {
                $allNotification = $allNotification->setOrder('entity_id', 'DESC')->getData();
            }
        }

        foreach ($allNotification as $key => $notification) {
            $notificationModel = $this->notificationTypeFactory->create();
            if (in_array($notification['notification_type'], self::NOTIFICATION_BOX_DEFAULT)) {
                $this->notificationTypeResource->load($notificationModel, $notification['notification_type'], 'default_type');
                $allNotification[$key]['notification_type'] = $notificationModel->getName();
            } else {
                $this->notificationTypeResource->load($notificationModel, $notification['notification_type'], 'entity_id');
                $allNotification[$key]['notification_type'] = $notificationModel->getName();
            }

            $allNotification[$key]['icon'] = $this->helper->getImageByNotificationType($notification);

            $allNotification[$key]['full_description'] = isset($notification['description']) ? $notification['description'] : '';

        }
        return $allNotification;
    }

    /**
     * @return array
     */
    public function getAllNotificationType()
    {
        $allNotificationType = [];
        $notificationType = $this->notificationTypeCollection->create()->addFieldToFilter('is_category', NotificationTypeModel::IS_CATEGORY);
        foreach ($notificationType as $item) {
            $allNotificationType[($item->getDefaultType() != 'null') ? $item->getDefaultType() : $item->getEntityId()] = $item->getName();
        }
        return $allNotificationType;
    }

    /**
     * @return $this|Notification
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $type = $this->getFilteredNotificationTypes();
        $pageLimit = $this->helper->getMaximumNotificationInMyNotificationOnMyAccountPage();
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Notification'));
        $this->getAllCustomerNotification($type);
        if ($this->notificationCollection) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'test'
            )->setAvailableLimit([$pageLimit => $pageLimit])
                ->setShowPerPage(true)->setCollection(
                    $this->notificationCollection
                );
            $this->setChild('pager', $pager);
            $this->notificationCollection->load();
        }
        return $this;
    }

    public function getFilteredNotificationTypes()
    {
        $params = $this->getRequest()->getParams();
        return (isset($params['type'])) ? $params['type'] : 'all';
    }

    /**
     * Get all notification ny notification type with is_category = true
     * @param $type
     * @return null
     */
    public function getAllCustomerNotification($type)
    {
        $pageLimit = $this->helper->getMaximumNotificationInMyNotificationOnMyAccountPage();
        if (!$this->notificationCollection && $customerId = $this->getCustomerId()) {
            $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
            $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : $pageLimit;
            $this->notificationCollection = $this->collectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId);
            if ($type != 'all') {
                $this->notificationCollection->addFieldToFilter('notification_type', $type);
            }
            $this->notificationCollection->setPageSize($pageSize);
            $this->notificationCollection->setCurPage($page);
        }
        return $this->notificationCollection;
    }

    /**
     * Get all notification type for filter
     * check customer is login or not
     * return customer id
     */
    public function getCustomerId()
    {
        if ($this->session->getCustomerId()) {
            return $this->session->getCustomerId();
        }
        return false;
    }
}
