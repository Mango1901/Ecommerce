<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 13/06/2022
 * Time: 15:46
 */
declare(strict_types=1);

namespace Magenest\NotificationBox\Observer;

use Exception;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\ResourceModel\Account\CollectionFactory as AffiliateCollectionFactory;
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
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SendNotificationAffiliateProgram implements ObserverInterface
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

    /** @var AffiliateCollectionFactory */
    protected $affiliateCollectionFactory;

    /** @var ResourceConnection */
    protected $resourceConnection;

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
        NotificationQueueFactory              $notificationQueueFactory,
        AffiliateCollectionFactory            $affiliateCollectionFactory,
        ResourceConnection                    $resourceConnection
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
        $this->affiliateCollectionFactory = $affiliateCollectionFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->helper->getEnableModule()) {
            return;
        }

        try {
            $affiliateCampaign = $observer->getEvent()->getDataObject();
            if ($affiliateCampaign && $affiliateCampaign->getId()) {
                $affiliateGroupIds = $affiliateCampaign->getAffiliateGroupIds();
                $affiliateGroupArrayIds = explode(',', $affiliateGroupIds);
                /**
                 * @var $affiliateCollection \Magenest\Affiliate\Model\ResourceModel\Account\Collection
                 */
                $affiliateCollection = $this->affiliateCollectionFactory->create();
                $affiliateCollection->addFieldToFilter(
                    'group_id',
                    [
                        'in' => $affiliateGroupArrayIds
                    ]
                )->addFieldToFilter(
                    'status',
                    [
                        'eq' => Status::ACTIVE
                    ]
                )->addFieldToSelect(['customer_id']);

                $customerIds = [];
                if ($affiliateCollection->count()) {
                    $customerIds = $affiliateCollection->getColumnValues('customer_id');
                }

                $listNotification = $this->collection
                    ->addFieldToFilter('is_active', Notification::ACTIVE)
                    ->addFieldToFilter('notification_type', Notification::AFFILIATE_PROGRAM)
                    ->getData();

                $customerToken = $this->tokenCollection->create()
                    ->addFieldToFilter('is_active', CustomerTokenModel::IS_ACTIVE)
                    ->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED)
                    ->addFieldToFilter('customer_id', ['in' => $customerIds]);

                foreach ($listNotification as $key => $notification) {
                    if ($notification['send_time'] == 'send_immediately') {
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
                $notificationQueueInsert = [];
                $notificationImmediatelyInsert = [];

                foreach ($listNotification as $key => $notification) {
                    foreach ($customerIds as $customerId) {
                        //add notification to queue
                        if (
                            $notification['send_time'] == 'schedule_time' ||
                            $notification['send_time'] == 'send_after_the_trigger_condition'
                        ) {
                            $notificationQueueInsert[] = [
                                'customer_id' => $customerId,
                                'id' => $notification['id'] ?? null,
                                'notification_type' => $notification['notification_type'] ?? null,
                                'name' => $notification['name'] ?? null,
                                'image' => $notification['image'] ?? null,
                                'store_view' => $notification['store_view'] ?? null,
                                'customer_group' => $notification['customer_group'] ?? null,
                                'description' => $notification['description'] ?? null,
                                'redirect_url' => $notification['redirect_url'] ?? null,
                                'send_time' => $notification['send_time'] ?? null,
                                'schedule' => $notification['schedule'] ?? null,
                                'is_sent' => $notification['is_sent'] ?? null,
                                'token' => $notification['token'] ?? null,
                            ];
                            continue;
                        }

                        if ($notification['send_time'] == 'send_immediately') {
                            $notificationImmediatelyInsert[] = [
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
                    }
                }

                $connection = $this->resourceConnection->getConnection();

                if (count($notificationImmediatelyInsert)) {
                    $customerNotificationTable = $connection->getTableName('magenest_customer_notification');
                    $connection->insertArray(
                        $customerNotificationTable,
                        ['notification_id', 'customer_id', 'status', 'star', 'icon', 'notification_type', 'condition', 'description', 'redirect_url'],
                        $notificationImmediatelyInsert
                    );
                }

                if (count($notificationQueueInsert)) {
                    $notificationQueueTable = $connection->getTableName('magenest_notification_queue');
                    $connection->insertArray(
                        $notificationQueueTable,
                        ['customer_id', 'id', 'notification_type', 'name', 'image', 'store_view', 'customer_group', 'description', 'redirect_url', 'send_time', 'schedule', 'is_sent', 'token'],
                        $notificationQueueInsert
                    );
                }
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
