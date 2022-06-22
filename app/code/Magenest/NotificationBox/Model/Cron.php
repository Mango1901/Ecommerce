<?php

namespace Magenest\NotificationBox\Model;

use Exception;
use Magenest\NotificationBox\Api\IsProductHavePromotionInterface;
use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\CustomerNotification as CustomerNotificationModel;
use Magenest\NotificationBox\Model\CustomerToken as CustomerTokenModel;
use Magenest\NotificationBox\Model\Notification as NotificationModel;
use Magenest\NotificationBox\Model\NotificationQueueFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\CollectionFactory;
use Magenest\NotificationBox\Model\ResourceModel\Notification;
use Magenest\NotificationBox\Model\ResourceModel\Notification\CollectionFactory as NotificationCollection;
use Magenest\NotificationBox\Model\ResourceModel\NotificationQueue;
use Magenest\NotificationBox\Model\ResourceModel\NotificationQueue\Collection as NotificationQueueCollection;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTime;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Reports\Model\ResourceModel\Quote\CollectionFactory as AbandonedCart;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManage;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistItemCollectionFactory;
use Psr\Log\LoggerInterface;

class Cron
{
    const LIST_NOTIFICATION_TYPE = [
        NotificationModel::REVIEW_REMINDERS,
        NotificationModel::ORDER_STATUS_UPDATE,
        NotificationModel::ABANDONED_CART_REMINDS,
        NotificationModel::REWARD_POINT_REMINDS,
        NotificationModel::STORE_CREDIT_REMINDS,
        NotificationModel::BIRTHDAY,
        NotificationModel::AFFILIATE_PROGRAM,
        NotificationModel::NEWSLETTER,
        NotificationModel::PRODUCT_WISHLIST_PROMOTIONS,
        NotificationModel::CUSTOMER_LOGIN,
        NotificationModel::MAINTENANCE
    ];
    /** @var CustomerTokenFactory */
    protected $customerTokenFactory;

    /** @var CustomerToken */
    protected $customerTokenResource;

    /** @var AbandonedCart */
    protected $abandonedCart;

    /** @var StoreManage */
    protected $listStore;

    /** @var LoggerInterface */
    protected $logger;

    /** @var NotificationCollection */
    protected $notificationCollection;

    /** @var Json */
    protected $serialize;

    /** @var DateTime */
    protected $dateTime;

    /** @var CustomerNotificationFactory */
    protected $customerNotificationFactory;

    /** @var Helper */
    protected $helper;

    /** @var Collection */
    protected $customerTokenCollection;

    /** @var CustomerNotification */
    protected $customerNotificationResource;

    /** @var NotificationFactory */
    protected $notificationFactory;

    /** @var Notification */
    protected $notificationResource;

    /** @var NotificationQueueCollection */
    protected $notificationQueueCollection;

    /**
     * @var \Magenest\NotificationBox\Model\NotificationQueueFactory
     */
    protected $notificationQueueFactory;

    /** @var NotificationQueue */
    protected $notificationQueue;

    /** @var ManagerInterface */
    protected $managerInterface;

    /** @var StoreRepositoryInterface */
    protected $storeRepositoryInterface;

    /** @var CustomerRepositoryInterface */
    protected $customerRepository;
    /**
     * URL builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;
    /**
     * Cart Repository
     *
     * @var CartRepositoryInterface
     */
    protected $cartRepository;
    /**
     * Cart Repository
     *
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var WishlistItemCollectionFactory
     */
    private $wishlistItemCollection;
    /**
     * @var IsProductHavePromotionInterface
     */
    private $isProductHavePromotion;

    /**
     * @param Helper $helper
     * @param AbandonedCart $abandonedCart
     * @param StoreManage $storeManager
     * @param LoggerInterface $logger
     * @param NotificationCollection $notificationCollection
     * @param Json $serialize
     * @param DateTime $dateTime
     * @param CustomerNotificationFactory $customerNotificationFactory
     * @param CollectionFactory $customerTokenCollection
     * @param CustomerToken $customerTokenResource
     * @param CustomerTokenFactory $customerTokenFactory
     * @param CustomerNotification $customerNotificationResource
     * @param NotificationFactory $notificationFactory
     * @param Notification $notificationResource
     * @param NotificationQueueCollection $notificationQueueCollection
     * @param \Magenest\NotificationBox\Model\NotificationQueueFactory $notificationQueueFactory
     * @param NotificationQueue $notificationQueue
     * @param ManagerInterface $managerInterface
     * @param StoreRepositoryInterface $storeRepositoryInterface
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param WishlistItemCollectionFactory $wishlistItemCollection
     * @param IsProductHavePromotionInterface $isProductHavePromotion
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Helper                          $helper,
        AbandonedCart                   $abandonedCart,
        StoreManage                     $storeManager,
        LoggerInterface                 $logger,
        NotificationCollection          $notificationCollection,
        Json                            $serialize,
        DateTime                        $dateTime,
        CustomerNotificationFactory     $customerNotificationFactory,
        CollectionFactory               $customerTokenCollection,
        CustomerToken                   $customerTokenResource,
        CustomerTokenFactory            $customerTokenFactory,
        CustomerNotification            $customerNotificationResource,
        NotificationFactory             $notificationFactory,
        Notification                    $notificationResource,
        NotificationQueueCollection     $notificationQueueCollection,
        NotificationQueueFactory        $notificationQueueFactory,
        NotificationQueue               $notificationQueue,
        ManagerInterface                $managerInterface,
        StoreRepositoryInterface        $storeRepositoryInterface,
        CustomerRepositoryInterface     $customerRepository,
        SearchCriteriaBuilder           $searchCriteriaBuilder,
        WishlistItemCollectionFactory   $wishlistItemCollection,
        IsProductHavePromotionInterface $isProductHavePromotion,
        UrlInterface                    $urlBuilder,
        CartRepositoryInterface         $cartRepository,
        OrderRepositoryInterface        $orderRepository
    ) {
        $this->storeRepositoryInterface = $storeRepositoryInterface;
        $this->managerInterface = $managerInterface;
        $this->notificationQueue = $notificationQueue;
        $this->notificationQueueFactory = $notificationQueueFactory;
        $this->notificationQueueCollection = $notificationQueueCollection;
        $this->notificationFactory = $notificationFactory;
        $this->notificationResource = $notificationResource;
        $this->customerNotificationResource = $customerNotificationResource;
        $this->customerTokenResource = $customerTokenResource;
        $this->customerTokenFactory = $customerTokenFactory;
        $this->customerTokenCollection = $customerTokenCollection;
        $this->helper = $helper;
        $this->listStore = $storeManager;
        $this->abandonedCart = $abandonedCart;
        $this->logger = $logger;
        $this->notificationCollection = $notificationCollection;
        $this->serialize = $serialize;
        $this->dateTime = $dateTime;
        $this->customerNotificationFactory = $customerNotificationFactory;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->wishlistItemCollection = $wishlistItemCollection;
        $this->isProductHavePromotion = $isProductHavePromotion;
        $this->_urlBuilder = $urlBuilder;
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Resets the notification limit received per day by customer
     */
    public function resetLimitNumberOfNotification()
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }
        $allCustomerToken = $this->customerTokenCollection->create();
        foreach ($allCustomerToken as $token) {
            try {
                $tokenModel = $this->customerTokenFactory->create();
                $this->customerTokenResource->load($tokenModel, $token->getEntityId());
                $tokenModel->setData('limit', 0);
                $this->customerTokenResource->save($tokenModel);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * Send notification reminder abandoned cart
     */
    public function reminderAbandonedCart()
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }
        $listNotification = $this->notificationCollection->create();
        $listNotification = $listNotification->addFieldToFilter('notification_type', 'abandoned_cart_reminds')
            ->addFieldToFilter('is_active', NotificationModel::ACTIVE)
            ->getData();

        $listStore = $this->getListStore();
        $allAbandonedCart = $this->abandonedCart->create()->prepareForAbandonedReport($listStore);

        //get current store date time
        $now = $this->dateTime->gmtDate();
        foreach ($allAbandonedCart as $item) {
            $customerId = $item->getCustomerId();
            $customerGroupId = $item->getCustomerGroupId();
            $storeId = $item->getStoreId();
            $timeUpdate = strtotime($item->getUpdatedAt());
            foreach ($listNotification as $notification) {
                $allowSend = true;
                $listTimeSent = [];
                $hour = $notification['condition'];
                // Get the time --$hour-- hour ago
                $remindDay = date('Y-m-d H:i:s', strtotime('-' . $hour . ' hour', strtotime($now)));
                if (isset($notification['time_sent'])) {
                    $listTimeSent = $this->serialize->unserialize($notification['time_sent']);
                    //$listTimeSent[$customerId]: the latest reminder time
                    if (isset($listTimeSent[$customerId]) && $remindDay < $listTimeSent[$customerId]) {
                        $allowSend = false;
                    }
                }

                if ($allowSend && strtotime($remindDay) >= $timeUpdate) {
                    $notificationModel = $this->notificationFactory->create();
                    $this->notificationResource->load($notificationModel, $notification['id']);

                    $listStoreView = $this->serialize->unserialize($notification['store_view']);
                    $listCustomerGroup = $this->serialize->unserialize($notification['customer_group']);
                    if (!in_array('0', $listStoreView) && !in_array($storeId, $listStoreView) ||
                        !in_array('0', $listCustomerGroup) && !in_array($customerGroupId, $listCustomerGroup)) {
                        continue;
                    }
                    //sent notification via firebase
                    $customerToken = $this->customerTokenCollection->create()->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('is_active', NotificationModel::ACTIVE)
                        ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED);
                    if ($customerToken) {
                        $tokenSent = [];
                        foreach ($customerToken as $token) {
                            $currentToken = ['token' => $token->getToken(), 'id' => $token->getGuestId()];
                            if (!in_array($currentToken, $tokenSent)) {
                                $this->helper->sendNotificationWithFireBase($notification, $token);
                            }
                            $tokenSent[] = $currentToken;
                        }
                    }
                    unset($notification['id']);
                    unset($notification['created_at']);
                    $this->saveCustomerNotification($notification, $customerId);
                    //update time sent
                    if (isset($notificationModel['time_sent']) && $notificationModel['time_sent'] !== null) {
                        $listTimeSent = $this->serialize->unserialize($notificationModel->getTimeSent());
                    }
                    $listTimeSent[$customerId] = $now;
                    $notificationModel->setTimeSent($this->serialize->serialize($listTimeSent));
                    $this->notificationResource->save($notificationModel);
                }
            }
        }
    }

    /** return array */
    public function getListStore()
    {
        $options = [];
        //$listStore = $this->listStore->getGroups();
        $listStore = $this->storeRepositoryInterface->getList();
        foreach ($listStore as $store) {
            $options[] = $store->getId();
        }
        return $options;
    }

    /**
     * @param $notification
     * @param $customerId
     */
    private function saveCustomerNotification($notification, $customerId)
    {
        try {
            unset($notification['created_at']);
            unset($notification['entity_id']);
            $notification['customer_id'] = $customerId;
            $notification['icon'] = $notification['image'];
            $notification['star'] = CustomerNotificationModel::UNSTAR;
            $notification['status'] = CustomerNotificationModel::STATUS_UNREAD;
            $customerNotification = $this->customerNotificationFactory->create();
            $customerNotification->addData($notification);
            $this->customerNotificationResource->save($customerNotification);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * Send scheduled and queue announcements to customers and guests
     */
    public function sendNotification()
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }
        $notificationSent = [];
        $notificationQueue = [];

        // Get all the notices to send
        $listNotification = $this->notificationCollection->create()
            ->addFieldToFilter('notification_type', array('nin' => self::LIST_NOTIFICATION_TYPE))
            ->addFieldToFilter('is_active', NotificationModel::ACTIVE)
            ->addFieldToFilter('is_sent', NotificationModel::IS_NOT_SENT)
            ->addFieldToFilter('send_time', ['neq' => 'send_immediately'])
            ->getData();

        $listNotificationQueue = $this->notificationQueueCollection
            ->addFieldToFilter(
                'is_sent',
                NotificationModel::IS_NOT_SENT
            )->getData();

        $allCustomer = $this->helper->getAllCustomer();

        $now = $this->dateTime->gmtDate();

        // Send custom notice
        foreach ($listNotification as $notification) {
            $notificationSent[] = $this->sendNotificationViaMagentoAndFireBase($notification, $now, $allCustomer);
        }

        // Send queue notice
        foreach ($listNotificationQueue as $notification) {
            $notificationQueue[] = $this->sendNotificationViaMagentoAndFireBase($notification, $now, $notification['customer_id']);
        }

        // Only send once
        if (count($notificationSent)) {
            foreach ($notificationSent as $item) {
                $notificationModel = $this->notificationFactory->create();
                $this->notificationResource->load($notificationModel, $item);
                if (count($notificationModel->getData()) > 0) {
                    if ($notificationModel->getIsSent() == NotificationModel::IS_NOT_SENT) {
                        $notificationModel->setData('is_sent', NotificationModel::IS_SENT);
                        try {
                            $this->notificationResource->save($notificationModel);
                        } catch (Exception $e) {
                            $this->logger->error($e->getMessage());
                        }
                    }
                }
            }
        }
        if (count($notificationQueue)) {
            foreach ($notificationQueue as $item) {
                if (isset($item)) {
                    $notificationQueueModel = $this->notificationQueueFactory->create();
                    $this->notificationQueue->load($notificationQueueModel, $item, 'id');
                    try {
                        $this->notificationQueue->delete($notificationQueueModel);
                    } catch (Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * send notification via Firebase and Magento
     * return list notification id sent
     * @param $notification
     * @param $now
     * @param $allCustomer
     * @return array
     * @throws AlreadyExistsException
     */
    private function sendNotificationViaMagentoAndFireBase($notification, $now, $allCustomer)
    {
        $id = null;
        try {
            //Send notifications to tokens that satisfy the condition via firebase
            $timeToSend = $this->getTimeToSendNotification($notification);
            if (isset($timeToSend) && $now >= $timeToSend) {
                if (is_string($allCustomer)) {
                    //send notice via magento
                    $this->saveCustomerNotification($notification, $notification['customer_id']);
                    //send notice via firebase
                    $tokens = ($notification['token']) ? $notification['token'] : $this->helper->getToken($notification);
                    $tokenSent = [];
                    foreach ($tokens as $token) {
                        $currentToken = ['token' => $token->getToken(), 'id' => $token->getGuestId()];
                        if (!in_array($currentToken, $tokenSent)) {
                            $this->helper->sendNotificationWithFireBase($notification, $token);
                        }
                        $tokenSent[] = $currentToken;
                    }
                    $id = $notification['id'];
                } elseif (isset($allCustomer)) {
                    //send to guest
                    $this->helper->sendNotificationWithFireBase($notification);
                    //send to customer
                    foreach ($allCustomer as $item) {
                        $customerId = $item->getEntityId();
                        $customerGroupId = $item->getGroupId();
                        $storeId = $item->getStoreId();

                        $listStoreView = $this->serialize->unserialize($notification['store_view']);
                        $listCustomerGroup = $this->serialize->unserialize($notification['customer_group']);
                        if (!in_array('0', $listStoreView) && !in_array($storeId, $listStoreView) ||
                            !in_array('0', $listCustomerGroup) && !in_array($customerGroupId, $listCustomerGroup)) {
                            continue;
                        }
                        $this->saveCustomerNotification($notification, $customerId);
                    }
                    $id = $notification['id'];
                } else {
                    if (isset($notification['token'])) {
                        $customerToken = $this->customerTokenCollection->create()
                            ->addFieldToFilter('token', $notification['token'])
                            ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED)
                            ->addFieldToFilter('is_active', CustomerTokenModel::IS_ACTIVE);
                        $this->helper->sendNotificationWithFireBase($notification, $customerToken->getFirstItem());
                        $id = $notification['id'];
                    }
                }
            }
        } catch (Exception $e) {
            $this->managerInterface->addErrorMessage($e->getMessage());
        }
        return $id;
    }

    /**
     * get time to send from notification
     * return date time
     * @param $notification
     * @return false|string
     */
    private function getTimeToSendNotification($notification)
    {
        if ($notification['send_time'] == 'schedule_time' && isset($notification['schedule'])) {
            $timeToSend = $notification['schedule'];
            $timeToSend = date("Y-m-d H:i:s", strtotime($timeToSend));
        } elseif ($notification['send_time'] == 'send_after_the_trigger_condition') {
            $scheduleTo = $this->serialize->unserialize($notification['schedule']);
            $sendAfter = $scheduleTo['send_after'];
            $unit = $scheduleTo['unit'];
            $timeToSend = date('Y-m-d H:i', strtotime('+' . $sendAfter . $unit, strtotime($notification['update_at'])));
        }
        return $timeToSend;
    }

    /**
     * @return void
     * @throws AlreadyExistsException
     */
    public function sendNotificationAfterSave()
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }

        $listNotification = $this->notificationCollection->create()
            ->addFieldToFilter('notification_type', array('nin' => self::LIST_NOTIFICATION_TYPE))
            ->addFieldToFilter('is_active', NotificationModel::ACTIVE)
            ->addFieldToFilter('is_sent', NotificationModel::IS_NOT_SENT)
            ->addFieldToFilter('send_time', ['eq' => 'send_immediately']);
        foreach ($listNotification as $notification) {
            $this->helper->sendNotificationInMagento($notification->getData());
            $this->helper->sendNotificationWithFireBase($notification->getData());
            $notification->setData('is_sent', NotificationModel::IS_SENT);
            $this->notificationResource->save($notification);
        }
    }

    /**
     * Send notification reminder customer birthday
     */
    public function reminderCustomerBirthday()
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }
        $listNotification = $this->notificationCollection->create();
        $listNotification = $listNotification
            ->addFieldToFilter(
                'notification_type',
                NotificationModel::BIRTHDAY
            )->addFieldToFilter(
                'is_active',
                NotificationModel::ACTIVE
            )->getData();

        foreach ($listNotification as $notification) {
            $listTimeSent = [];
            $sentBefore = $notification['condition'];
            $customerItems = $this->getCustomerBirthdayBeforeDay($sentBefore);
            if (is_array($customerItems) && count($customerItems)) {
                $listCustomerGroup = $this->serialize->unserialize($notification['customer_group']);
                $notificationModel = $this->notificationFactory->create();
                $this->notificationResource->load($notificationModel, $notification['id']);

                /**
                 * @var $customer CustomerInterface
                 */
                foreach ($customerItems as $customer) {
                    if (!in_array('0', $listCustomerGroup) && !in_array($customer->getGroupId(), $listCustomerGroup)) {
                        continue;
                    }
                    $customerId = $customer->getId();
                    //sent notification via firebase
                    $customerToken = $this->customerTokenCollection->create()
                        ->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('is_active', NotificationModel::ACTIVE)
                        ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED);
                    if ($customerToken) {
                        $tokenSent = [];
                        foreach ($customerToken as $token) {
                            $currentToken = ['token' => $token->getToken(), 'id' => $token->getGuestId()];
                            if (!in_array($currentToken, $tokenSent)) {
                                $this->helper->sendNotificationWithFireBase($notification, $token);
                            }
                            $tokenSent[] = $currentToken;
                        }
                    }
                    unset($notification['id']);
                    unset($notification['created_at']);
                    $this->saveCustomerNotification($notification, $customerId);
                    // Update time sent
                    if (isset($notificationModel['time_sent']) && $notificationModel['time_sent'] !== null) {
                        $listTimeSent = $this->serialize->unserialize($notificationModel->getTimeSent());
                    }
                    $listTimeSent[$customerId] = $this->dateTime->gmtDate();
                    $notificationModel->setTimeSent($this->serialize->serialize($listTimeSent));
                    $this->notificationResource->save($notificationModel);
                }
            }
        }
    }

    /**
     * @param $sentBefore
     * @return CustomerInterface[]|null
     */
    private function getCustomerBirthdayBeforeDay($sentBefore)
    {
        try {
            $now = $this->dateTime->gmtDate();
            $afterDayFromNow = strtotime($now . " + $sentBefore days");
            $dayCondition = '%-' . date('m-d', $afterDayFromNow);
            $this->searchCriteriaBuilder->addFilter(
                CustomerInterface::DOB,
                $dayCondition,
                'like'
            );

            return $this->customerRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * Send notification reminder customer product in wishlist have promotion
     * @return void
     */
    public function reminderProductWishlistPromotions()
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }

        $listNotification = $this->notificationCollection->create();

        $connection = $this->notificationResource->getConnection();
        $wishlistTable = $connection->getTableName('wishlist');

        $wishlistItemCollection = $this->wishlistItemCollection->create()->join(
            ['wishlist' => $wishlistTable],
            'main_table.wishlist_id = wishlist.wishlist_id',
            ['customer_id']
        );

        $listNotification = $listNotification
            ->addFieldToFilter(
                'notification_type',
                NotificationModel::PRODUCT_WISHLIST_PROMOTIONS
            )->addFieldToFilter(
                'is_active',
                NotificationModel::ACTIVE
            )->getData();


        $notificationCustomerInsert = [];
        foreach ($listNotification as $notification) {
            $listTimeSent = [];
            $notificationModel = $this->notificationFactory->create();
            $this->notificationResource->load($notificationModel, $notification['id']);

            if (isset($notificationModel['time_sent']) && $notificationModel['time_sent'] !== null) {
                $listTimeSent = $this->serialize->unserialize($notificationModel->getTimeSent());
            }

            /** @var Item $item */
            foreach ($wishlistItemCollection->getItems() as $item) {
                if ($this->isProductHavePromotion->execute($item->getProduct())) {
                    $customerId = $item->getCustomerId();
                    $productName = $item->getProduct()->getName();
                    $customerToken = $this->customerTokenCollection->create()
                        ->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('is_active', NotificationModel::ACTIVE)
                        ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED);
                    $notification['description'] = __('Price of product %1 has been discounted in wishlist of you.', $productName);

                    if ($customerToken) {
                        $tokenSent = [];
                        foreach ($customerToken as $token) {
                            $currentToken = ['token' => $token->getToken(), 'id' => $token->getGuestId()];
                            if (!in_array($currentToken, $tokenSent)) {
                                $this->helper->sendNotificationWithFireBase($notification, $token);
                            }
                            $tokenSent[] = $currentToken;
                        }
                    }
                    // Update time sent
                    $listTimeSent[$customerId] = $this->dateTime->gmtDate();

                    $notificationCustomerInsert[] = [
                        'notification_id' => $notification['id'] ?? null,
                        'customer_id' => $customerId,
                        'status' => CustomerNotificationModel::STATUS_UNREAD,
                        'star' => CustomerNotificationModel::UNSTAR,
                        'icon' => $notification['icon'] ?? null,
                        'notification_type' => $notification['notification_type'] ?? null,
                        'condition' => $notification['condition'] ?? null,
                        'description' => $notification['description'] ?? null,
                        'redirect_url' => $notification['redirect_url'] ?? $this->_urlBuilder->getUrl('wishlist/')
                    ];
                }
            }
            $notificationModel->setTimeSent($this->serialize->serialize($listTimeSent));
            $this->notificationResource->save($notificationModel);
        }

        if (count($notificationCustomerInsert)) {
            $customerNotificationTable = $connection->getTableName('magenest_customer_notification');
            $connection->insertArray(
                $customerNotificationTable,
                ['notification_id', 'customer_id', 'status', 'star', 'icon', 'notification_type', 'condition', 'description', 'redirect_url'],
                $notificationCustomerInsert
            );
        }
    }

    /**
     * @return void
     * @throws AlreadyExistsException
     */
    public function reminderCustomerLogin()
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }

        $listNotification = $this->notificationCollection->create();

        $listNotification = $listNotification
            ->addFieldToFilter(
                'notification_type',
                NotificationModel::CUSTOMER_LOGIN
            )->addFieldToFilter(
                'is_active',
                NotificationModel::ACTIVE
            )->getData();

        $now = $this->dateTime->gmtDate();
        $notificationCustomerInsert = [];

        foreach ($listNotification as $notification) {
            $listTimeSent = [];
            $sentTimeAfterLogin = $notification['condition'];
            $beforeDayFromNow = date('Y-m-d H:i:s', strtotime($now . " - $sentTimeAfterLogin days"));
            $notificationModel = $this->notificationFactory->create();
            $this->notificationResource->load($notificationModel, $notification['id']);
            if (isset($notificationModel['time_sent']) && $notificationModel['time_sent'] !== null) {
                $listTimeSent = $this->serialize->unserialize($notificationModel->getTimeSent());
            }

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('is_active', 1)
                ->addFilter('updated_at', $beforeDayFromNow, 'lt')
                ->addFilter('items_qty', 0)
                ->addFilter('customer_id', null, 'neq')
                ->create();
            $listQuoteCustomerNoLongerUpdated = $this->cartRepository->getList($searchCriteria);
            $notification['description'] = __('Your cart has been empty for %1 days. Search products and shop with Kangaroo Shopping.', $sentTimeAfterLogin);

            foreach ($listQuoteCustomerNoLongerUpdated->getItems() as $quote) {
                $customerId = $quote->getCustomerId();

                $customerToken = $this->customerTokenCollection->create()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('is_active', NotificationModel::ACTIVE)
                    ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED);

                if ($customerToken) {
                    $tokenSent = [];
                    foreach ($customerToken as $token) {
                        $currentToken = ['token' => $token->getToken(), 'id' => $token->getGuestId()];
                        if (!in_array($currentToken, $tokenSent)) {
                            $this->helper->sendNotificationWithFireBase($notification, $token);
                        }
                        $tokenSent[] = $currentToken;
                    }
                }
                // Update time sent
                $listTimeSent[$customerId] = $this->dateTime->gmtDate();

                $notificationCustomerInsert[] = [
                    'notification_id' => $notification['id'] ?? null,
                    'customer_id' => $customerId,
                    'status' => CustomerNotificationModel::STATUS_UNREAD,
                    'star' => CustomerNotificationModel::UNSTAR,
                    'icon' => $notification['icon'] ?? null,
                    'notification_type' => $notification['notification_type'] ?? null,
                    'condition' => $notification['condition'] ?? null,
                    'description' => $notification['description'] ?? null,
                    'redirect_url' => $notification['redirect_url'] ?? null
                ];
            }

            $notificationModel->setTimeSent($this->serialize->serialize($listTimeSent));
            $this->notificationResource->save($notificationModel);
        }

        if (count($notificationCustomerInsert)) {
            $connection = $this->notificationResource->getConnection();
            $customerNotificationTable = $connection->getTableName('magenest_customer_notification');
            $connection->insertArray(
                $customerNotificationTable,
                ['notification_id', 'customer_id', 'status', 'star', 'icon', 'notification_type', 'condition', 'description', 'redirect_url'],
                $notificationCustomerInsert
            );
        }
    }

    /**
     * @return void
     * @throws AlreadyExistsException
     */
    public function reminderMaintenanceProduct()
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }

        $listNotification = $this->notificationCollection->create();

        $listNotification = $listNotification
            ->addFieldToFilter(
                'notification_type',
                NotificationModel::MAINTENANCE
            )->addFieldToFilter(
                'is_active',
                NotificationModel::ACTIVE
            )->getData();

        $now = $this->dateTime->gmtDate();
        $notificationCustomerInsert = [];

        foreach ($listNotification as $notification) {
            $listTimeSent = [];
            $sentTimeAfterBuy = $notification['condition'];
            $beforeDayFromNow = date('Y-m-d H:i:s', strtotime($now . " - $sentTimeAfterBuy days"));
            $notificationModel = $this->notificationFactory->create();
            $this->notificationResource->load($notificationModel, $notification['id']);
            if (isset($notificationModel['time_sent']) && $notificationModel['time_sent'] !== null) {
                $listTimeSent = $this->serialize->unserialize($notificationModel->getTimeSent());
            }

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('updated_at', $beforeDayFromNow, 'lt')
                ->addFilter('customer_id', null, 'neq')
                ->addFilter('status', 'complete')
                ->addFilter('state', 'complete')
                ->create();

            $listOrderCompleted = $this->orderRepository->getList($searchCriteria);

            /**
             * @var $order OrderInterface
             */
            foreach ($listOrderCompleted->getItems() as $order) {
                $notification['description'] = __('The date of warranty, maintenance and replacement of accessories for order #%1 has arrived. Please contact Kangaroo Shopping for advice.', $order->getIncrementId());

                $customerId = $order->getCustomerId();

                $customerToken = $this->customerTokenCollection->create()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('is_active', NotificationModel::ACTIVE)
                    ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED);

                if ($customerToken) {
                    $tokenSent = [];
                    foreach ($customerToken as $token) {
                        $currentToken = ['token' => $token->getToken(), 'id' => $token->getGuestId()];
                        if (!in_array($currentToken, $tokenSent)) {
                            $this->helper->sendNotificationWithFireBase($notification, $token);
                        }
                        $tokenSent[] = $currentToken;
                    }
                }
                // Update time sent
                $listTimeSent[$customerId] = $this->dateTime->gmtDate();

                $notificationCustomerInsert[] = [
                    'notification_id' => $notification['id'] ?? null,
                    'customer_id' => $customerId,
                    'status' => CustomerNotificationModel::STATUS_UNREAD,
                    'star' => CustomerNotificationModel::UNSTAR,
                    'icon' => $notification['icon'] ?? null,
                    'notification_type' => $notification['notification_type'] ?? null,
                    'condition' => $notification['condition'] ?? null,
                    'description' => $notification['description'] ?? null,
                    'redirect_url' => $notification['redirect_url'] ?? null
                ];
            }

            $notificationModel->setTimeSent($this->serialize->serialize($listTimeSent));
            $this->notificationResource->save($notificationModel);
        }

        if (count($notificationCustomerInsert)) {
            $connection = $this->notificationResource->getConnection();
            $customerNotificationTable = $connection->getTableName('magenest_customer_notification');
            $connection->insertArray(
                $customerNotificationTable,
                ['notification_id', 'customer_id', 'status', 'star', 'icon', 'notification_type', 'condition', 'description', 'redirect_url'],
                $notificationCustomerInsert
            );
        }
    }
}
