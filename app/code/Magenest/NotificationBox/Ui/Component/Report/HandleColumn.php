<?php

namespace Magenest\NotificationBox\Ui\Component\Report;

use Magenest\NotificationBox\Model\Notification;
use Magenest\NotificationBox\Model\NotificationTypeFactory;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType as notificationTypeResource;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\Collection;

/**
 * Class Image
 * @package Magenest\NotificationBox\Ui\Component\Listing\Columns
 */
class HandleColumn extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NOTIFICATION_TYPE_DEFAULT = [
        Notification::ORDER_STATUS_UPDATE_LABEL,
        Notification::REVIEW_REMINDERS_LABEL,
        Notification::ABANDONED_CART_REMINDS,
        Notification::REWARD_POINT_REMINDS,
        Notification::STORE_CREDIT_REMINDS,
        Notification::BIRTHDAY,
        Notification::AFFILIATE_PROGRAM,
        Notification::NEWSLETTER,
        Notification::PRODUCT_WISHLIST_PROMOTIONS,
    ];

    /** @var Collection */
    protected $collection;

    /** @var NotificationTypeFactory  */
    protected $notificationTypeFactory;

    /** @var notificationTypeResource  */
    protected $notificationTypeResource;

    /**
     * @param NotificationTypeFactory $notificationTypeFactory
     * @param notificationTypeResource $notificationTypeResource
     * @param Collection $collection
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        NotificationTypeFactory $notificationTypeFactory,
        notificationTypeResource $notificationTypeResource,
        Collection $collection,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->notificationTypeFactory = $notificationTypeFactory;
        $this->notificationTypeResource = $notificationTypeResource;
        $this->collection = $collection;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => $item) {
                $ctr = "0";
                if (isset($item['impression']) && isset($item['total_click'])) {
                    if ($item['impression'] != 0) {
                        $ctr = ($item['total_click'] / $item['impression']) * 100;
                        $ctr = round($ctr, 1, PHP_ROUND_HALF_DOWN);
                    }
                }
                $dataSource['data']['items'][$key]['ctr'] = $ctr;
                if (isset($item['notification_type'])) {
                    if (in_array($item['notification_type'], self::NOTIFICATION_TYPE_DEFAULT)) {
                        $dataSource['data']['items'][$key]['notification_type'] = $item['notification_type'];
                    } else {
                        $notificationModel = $this->notificationTypeFactory->create();
                        $this->notificationTypeResource->load($notificationModel,$item['notification_type']);
                        $dataSource['data']['items'][$key]['notification_type'] =$notificationModel->getName();
                    }
                }
            }
        }
        return $dataSource;
    }
}
