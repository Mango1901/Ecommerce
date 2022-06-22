<?php
namespace Magenest\NotificationBox\Model\Api;

use Magenest\NotificationBox\Api\GetNotificationInterface;
use \Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\Collection;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class GetNotification implements GetNotificationInterface
{
    /** @var Helper  */
    public $helper;

    /** @var CollectionFactory  */
    public $collectionFactory;

    /** @var TimezoneInterface  */
    protected $timezoneInterface;

    /** @var UrlInterface  */
    protected $urlInterface;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var CustomerRepositoryInterface  */
    protected $customerRepositoryInterface;

    /**
     * @param Helper $helper
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezoneInterface
     * @param UrlInterface $urlInterface
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        Helper $helper,
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezoneInterface,
        UrlInterface $urlInterface,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepositoryInterface)
    {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->logger                   = $logger;
        $this->urlInterface             = $urlInterface;
        $this->timezoneInterface        = $timezoneInterface;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
    }

    /**
     * @param int $customerId
     * @return array
     */
    public function getCustomerNotification($customerId){
        $data = [];
        $notificationCollection = $this->getNotifications($customerId);
        $notificationCollectionClone = clone $notificationCollection;
        $notificationCollection = $notificationCollection->setPageSize(20);
        $notificationCollection = $notificationCollection->getData();
        try {
            $this->customerRepositoryInterface->getById($customerId);
            foreach ($notificationCollection as & $notification){
                $notification['name'] = $this->helper->getNotificationNameById($notification['notification_id']);
                $notification['icon'] = $this->helper->getImageByNotificationType($notification);
                $notification['created_at'] = $this->timezoneInterface->formatDateTime($notification['created_at'],2,2);
                $notification['redirect_url'] = $this->urlInterface->getUrl('notibox/handleNotification/viewNotification').'?id='.$notification['entity_id'];
            }
            $totalNotificationUnread = $notificationCollectionClone->addFieldToFilter('status',0)->getSize();
            $data[] = [
                'status'=>true,
                'data'=>[
                    'totalNotificationUnread'=>$totalNotificationUnread,
                    'allNotification'=>$notificationCollection
                ]
            ];
        }
        catch (\Exception $exception){
            $this->logger->error($exception->getMessage());
            $data[] = [
                    'status'=>false,
                    'message'=>$exception->getMessage()
                ];
        }

        return $data;
    }

    /**
     * @param $customerId
     * @return Collection
     */
    public function getNotifications($customerId){
        return $this->collectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->setOrder('entity_id','DESC');
    }
}
