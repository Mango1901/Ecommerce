<?php

namespace Magenest\NotificationBox\Model\Api;

use Magenest\NotificationBox\Api\HandleNotificationInterface;
use \Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\CustomerNotification as CustomerNotificationModel;
use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\CollectionFactory;
use Psr\Log\LoggerInterface;

class HandleNotification implements HandleNotificationInterface
{
    /** @var Helper */
    public $helper;

    /** @var CustomerNotificationFactory  */
    public $notificationModel;

    /** @var CustomerNotification  */
    public $notificationResource;

    /** @var CollectionFactory */
    public $notificationCollection;

    /** @var LoggerInterface  */
    public $logger;

    /**
     * @param Helper $helper
     * @param CustomerNotificationFactory $notificationFactory
     * @param CustomerNotification $notificationResource
     * @param CollectionFactory $notificationCollection
     * @param LoggerInterface $logger
     */
    public function __construct(
        Helper $helper,
        CustomerNotificationFactory
        $notificationFactory,
        CustomerNotification $notificationResource,
        CollectionFactory $notificationCollection,
        LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->notificationModel = $notificationFactory;
        $this->notificationResource = $notificationResource;
        $this->notificationCollection = $notificationCollection;
    }

    /** delete notifications
     * @param int $customerId
     * @param string $notificationId
     * @return array
     */
    public function deleteNotifications($customerId,$notificationId)
    {
        $data = [];
        if($notificationId=="" && strtolower($notificationId)!="all"){
            $data[] = [
                'status'=>false,
                'message'=>__('Invalid notification id(s)')
            ];
        }
        else{
            $notificationCollection = $this->getCollection($customerId,$notificationId);
            if($notificationCollection->getSize() == 0){
                $data[] = [
                    'status'=>false,
                    'message'=>__('Notification(s) cannot be found according to the input condition')
                ];
            }
            else{
                foreach ($notificationCollection as $notification){
                    try {
                        $this->notificationResource->delete($notification);
                    } catch (\Exception $exception) {
                        $this->logger->error($exception->getMessage());
                        return $data[] = [
                            'status'=>false,
                            'message'=>$exception->getMessage()
                        ];
                    }
                }
                $data[] = [
                    'status'=>true,
                    'message'=>__('Delete notification(s) success')
                ];
            }
        }
        return $data;
    }

    /** mark as read notifications
     * @param int $customerId
     * @param string $notificationId
     * @return array
     */
    public function markAsRead($customerId,$notificationId){
        return $this->updateNotification($customerId,$notificationId,'status',1);
    }

    /** mark important notifications
     * @param int $status
     * @param int $customerId
     * @param string $notificationId
     * @return array
     */
    public function markImportant($customerId,$notificationId,$status){
        return $this->updateNotification($customerId,$notificationId,'star',$status);
    }


    /**
     * update notification
     * @param $customerId
     * @param $notificationId
     * @param $status
     * @param $column
     * @return array
     */
    public function updateNotification($customerId,$notificationId,$column,$status){
        $data = [];
        if($notificationId==""){
            $data[] = [
                'status'=>false,
                'message'=>__('Notification(s) cannot be found according to the input condition')
            ];
        }else{
            try {
                $notificationCollection = $this->getCollection($customerId,$notificationId);
                if(count($notificationCollection) == 0){
                    $data[] = [
                        'status'=>false,
                        'message'=>__('Notification(s) cannot be found according to the input condition')
                    ];
                }else{
                    foreach ($notificationCollection as $item) {
                        $item->setData($column,$status);
                        $this->notificationResource->save($item);
                    }
                    $data[] = [
                        'status'=>true,
                        'message'=>__('Update notification(s) success.')
                    ];
                }

            }
            catch (\Exception $exception){
                $this->logger->error($exception->getMessage());
                return  [
                    ['status'=>false,
                    'message'=>$exception->getMessage()]
                ];
            }
        }
        return $data;
    }

    /**
     * get collection by condition
     * @param $customerId
     * @param $notificationId
     * @return array|CustomerNotification\Collection
     */
    public function getCollection($customerId,$notificationId){
        $notificationCollection = $this->notificationCollection->create()->addFieldToFilter('customer_id',$customerId);
        if($notificationId ==""){
            return [];
        }else {
            $notificationId = strtolower($notificationId);
            if ($notificationId != "all") {
                $listNotificationId = explode(',', $notificationId);
                $notificationCollection->addFieldToFilter('entity_id', ['in' => $listNotificationId]);
            }
            $notificationCollection->addFieldToFilter('customer_id', $customerId);
        }
        return $notificationCollection;
    }
}
