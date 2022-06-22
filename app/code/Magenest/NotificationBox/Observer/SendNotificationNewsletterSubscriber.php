<?php

namespace Magenest\NotificationBox\Observer;

use Exception;
use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\CustomerNotification as CustomerNotificationModel;
use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magenest\NotificationBox\Model\CustomerToken as CustomerTokenModel;
use Magenest\NotificationBox\Model\CustomerTokenFactory;
use Magenest\NotificationBox\Model\Notification;
use Magenest\NotificationBox\Model\NotificationQueueFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\CollectionFactory as CustomerNotificationCollectionFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\CollectionFactory as Collection;
use Magenest\NotificationBox\Model\ResourceModel\Notification\CollectionFactory;
use Magenest\NotificationBox\Model\ResourceModel\NotificationQueue;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SendNotificationNewsletterSubscriber implements ObserverInterface
{
    /** @var Helper */
    protected $helper;

    /** @var CollectionFactory */
    protected $collection;

    /** @var Json */
    protected $serialize;

    /** @var CustomerNotificationCollectionFactory */
    protected $customerNotification;

    /** @var CustomerNotification */
    protected $customerNotificationResource;

    /** @var CustomerNotificationFactory */
    protected $customerNotificationFactory;

    /** @var CustomerTokenFactory */
    protected $customerTokenFactory;

    /** @var CustomerToken */
    protected $customerTokenResource;

    /** @var Collection */
    protected $tokenCollection;

    /** @var LoggerInterface */
    protected $logger;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var NotificationQueueFactory */
    protected $notificationQueueFactory;

    /** @var NotificationQueue */
    protected $notificationQueue;

    /** @var Session */
    protected $checkoutSession;

    /**
     * @param CustomerNotification $customerNotificationResource
     * @param Collection $tokenCollection
     * @param CustomerTokenFactory $customerTokenFactory
     * @param CustomerToken $customerTokenResource
     * @param CollectionFactory $collection
     * @param Json $serialize
     * @param CustomerNotificationCollectionFactory $customerNotification
     * @param CustomerNotificationFactory $customerNotificationFactory
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param NotificationQueue $notificationQueue
     * @param NotificationQueueFactory $notificationQueueFactory
     * @param Session $checkoutSession
     */
    public function __construct(
        CustomerNotification                  $customerNotificationResource,
        Collection                            $tokenCollection,
        CustomerTokenFactory                  $customerTokenFactory,
        CustomerToken                         $customerTokenResource,
        CollectionFactory                     $collection,
        Json                                  $serialize,
        CustomerNotificationCollectionFactory $customerNotification,
        CustomerNotificationFactory           $customerNotificationFactory,
        Helper                                $helper,
        LoggerInterface                       $logger,
        StoreManagerInterface                 $storeManager,
        NotificationQueue                     $notificationQueue,
        NotificationQueueFactory              $notificationQueueFactory,
        Session                               $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->notificationQueue = $notificationQueue;
        $this->notificationQueueFactory = $notificationQueueFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->customerNotificationResource = $customerNotificationResource;
        $this->tokenCollection = $tokenCollection;
        $this->helper = $helper;
        $this->customerTokenResource = $customerTokenResource;
        $this->customerTokenFactory = $customerTokenFactory;
        $this->customerNotificationFactory = $customerNotificationFactory;
        $this->serialize = $serialize;
        $this->collection = $collection->create();
        $this->customerNotification = $customerNotification;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }
        /**
         * @var Subscriber $subscriber
         */
        try {
            $subscriber = $observer->getEvent()->getSubscriber();
            $customerId = $subscriber->getCustomerId();
            $subscriberStatus = $subscriber->getSubscriberStatus();
            $storeId = $subscriber->getStoreId();
            $listNotification = $this->collection
                ->addFieldToFilter(
                    'is_active',
                    Notification::ACTIVE
                )->addFieldToFilter(
                    'notification_type',
                    [Notification::NEWSLETTER]
                )->getData();

            $customerToken = $this->tokenCollection->create()
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('is_active', CustomerTokenModel::IS_ACTIVE)
                ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED)
                ->addFieldToFilter('customer_id', $customerId);

            foreach ($listNotification as $key => $notification) {
                //remove notification if not meet conditions
                $listStore = $this->serialize->unserialize($notification['store_view']);
                if (!in_array('0', $listStore) && !in_array($storeId, $listStore)) {
                    unset($listNotification[$key]);
                    continue;
                }

                //add notification to queue
                if (
                    $notification['send_time'] == 'schedule_time' ||
                    $notification['send_time'] == 'send_after_the_trigger_condition'
                ) {
                    unset($notification['created_at']);
                    unset($notification['update_at']);
                    $notification['customer_id'] = $customerId;
                    $notification['description'] = $this->getSubscriberStatus($subscriberStatus);
                    $notificationQueueModel = $this->notificationQueueFactory->create();
                    $notificationQueueModel->addData($notification);
                    $this->notificationQueue->save($notificationQueueModel);
                    continue;
                }

                if (
                    $notification['send_time'] == 'send_immediately'
                ) {
                    unset($notification['created_at']);
                    $notification['customer_id'] = $customerId;
                    $notification['icon'] = $notification['image'];
                    $notification['star'] = CustomerNotificationModel::UNSTAR;
                    $notification['status'] = CustomerNotificationModel::STATUS_UNREAD;
                    $notification['description'] = $this->getSubscriberStatus($subscriberStatus);
                    $customerNotification = $this->customerNotificationFactory->create();
                    $customerNotification->addData($notification);
                    $this->customerNotificationResource->save($customerNotification);

                    $tokenSent = [];
                    foreach ($customerToken as $token) {
                        $currentToken = ['token' => $token->getToken(), 'id' => $token->getGuestId()];
                        if (!in_array($currentToken, $tokenSent)) {
                            $this->helper->sendNotificationWithFireBase($notification, $token);
                        }
                        $tokenSent[] = $currentToken;
                    }
                }
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param $status
     * @return \Magento\Framework\Phrase|string
     */
    private function getSubscriberStatus($status)
    {
        $message = __('We have updated your subscription.');
        if (Subscriber::STATUS_SUBSCRIBED === $status) {
            $message = __('We have saved your subscription.');
        } else if (Subscriber::STATUS_UNSUBSCRIBED === $status) {
            $message = __('We have removed your newsletter subscription.');
        }

        return $message;
    }
}
