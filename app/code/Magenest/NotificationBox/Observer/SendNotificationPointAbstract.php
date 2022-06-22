<?php

namespace Magenest\NotificationBox\Observer;

use Magenest\NotificationBox\Model\CustomerToken as CustomerTokenModel;
use Magenest\NotificationBox\Model\Notification;
use Magento\Framework\Event\ObserverInterface;
use Magenest\NotificationBox\Model\ResourceModel\Notification\CollectionFactory;
use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\CollectionFactory as CustomerNotificationCollectionFactory;
use Magenest\NotificationBox\Model\CustomerTokenFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\CollectionFactory as Collection;
use Magenest\NotificationBox\Helper\Helper;
use Psr\Log\LoggerInterface;
use Magenest\NotificationBox\Model\CustomerNotification as CustomerNotificationModel;
use Magenest\NotificationBox\Model\ResourceModel\NotificationQueue;
use Magenest\NotificationBox\Model\NotificationQueueFactory;

abstract class SendNotificationPointAbstract implements ObserverInterface
{
    /** @var Helper */
    protected $helper;

    /** @var CollectionFactory */
    protected $collection;

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

    /** @var NotificationQueueFactory */
    protected $notificationQueueFactory;

    /** @var NotificationQueue */
    protected $notificationQueue;

    /**
     * @param CustomerNotification $customerNotificationResource
     * @param Collection $tokenCollection
     * @param CustomerTokenFactory $customerTokenFactory
     * @param CustomerToken $customerTokenResource
     * @param CollectionFactory $collection
     * @param CustomerNotificationCollectionFactory $customerNotification
     * @param CustomerNotificationFactory $customerNotificationFactory
     * @param Helper $helper
     * @param LoggerInterface $logger
     * @param NotificationQueue $notificationQueue
     * @param NotificationQueueFactory $notificationQueueFactory
     */
    public function __construct(
        CustomerNotification                  $customerNotificationResource,
        Collection                            $tokenCollection,
        CustomerTokenFactory                  $customerTokenFactory,
        CustomerToken                         $customerTokenResource,
        CollectionFactory                     $collection,
        CustomerNotificationCollectionFactory $customerNotification,
        CustomerNotificationFactory           $customerNotificationFactory,
        Helper                                $helper,
        LoggerInterface                       $logger,
        NotificationQueue                     $notificationQueue,
        NotificationQueueFactory              $notificationQueueFactory
    ) {
        $this->notificationQueue = $notificationQueue;
        $this->notificationQueueFactory = $notificationQueueFactory;
        $this->logger = $logger;
        $this->customerNotificationResource = $customerNotificationResource;
        $this->tokenCollection = $tokenCollection;
        $this->helper = $helper;
        $this->customerTokenResource = $customerTokenResource;
        $this->customerTokenFactory = $customerTokenFactory;
        $this->customerNotificationFactory = $customerNotificationFactory;
        $this->collection = $collection->create();
        $this->customerNotification = $customerNotification;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }

        try {
            $transaction = $observer->getEvent()->getDataObject();
            $customerId = $transaction->getCustomerId();

            $listNotification = $this->collection
                ->addFieldToFilter('is_active', Notification::ACTIVE)
                ->addFieldToFilter('notification_type', $this->getNotificationType())
                ->getData();

            $customerToken = $this->tokenCollection->create()
                ->addFieldToFilter('is_active', CustomerTokenModel::IS_ACTIVE)
                ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED)
                ->addFieldToFilter('customer_id', $customerId);

            foreach ($listNotification as $key => $notification) {
                //add notification to queue
                if ($notification['send_time'] == 'schedule_time' ||
                    $notification['send_time'] == 'send_after_the_trigger_condition'
                ) {
                    unset($notification['created_at']);
                    unset($notification['update_at']);
                    $notification['customer_id'] = $customerId;
                    $notificationQueueModel = $this->notificationQueueFactory->create();
                    $notificationQueueModel->addData($notification);
                    $this->notificationQueue->save($notificationQueueModel);
                    continue;
                }

                if ($notification['send_time'] == 'send_immediately') {
                    unset($notification['created_at']);
                    $notification['customer_id'] = $customerId;
                    $notification['icon'] = $notification['image'];
                    $notification['star'] = CustomerNotificationModel::UNSTAR;
                    $notification['status'] = CustomerNotificationModel::STATUS_UNREAD;;
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
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @return array
     */
    abstract protected function getNotificationType();
}
