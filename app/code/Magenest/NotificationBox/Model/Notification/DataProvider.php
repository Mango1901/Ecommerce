<?php

namespace Magenest\NotificationBox\Model\Notification;

use Exception;
use Magenest\NotificationBox\Model\Notification;
use Magenest\NotificationBox\Model\ResourceModel\Notification\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{

    /** @var DataPersistorInterface */
    protected $dataPersistor;

    /** @var CollectionFactory */
    protected $collection;

    /** @var Json */
    protected $serialize;

    /** @var $loadedData */
    protected $loadedData;


    /**
     * Constructor
     *
     * @param Json $serialize
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        Json                   $serialize,
                               $name,
                               $primaryFieldName,
                               $requestFieldName,
        CollectionFactory      $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array                  $meta = [],
        array                  $data = []
    ) {
        $this->serialize = $serialize;
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            if ($model->getData('image')) {
                $model->setData('image', $this->serialize->unserialize($model->getImage()));
            }

            if ($model->getData('store_view')) {
                $model->setData('store_view', $this->serialize->unserialize($model->getStoreView()));
            }

            if ($model->getData('customer_group')) {
                $model->setData('customer_group', $this->serialize->unserialize($model->getCustomerGroup()));
            }

            if ($model->getData('notification_type')) {
                if ($model->getData('notification_type') == Notification::ORDER_STATUS_UPDATE) {
                    $model->setData('order_status', $this->serialize->unserialize($model->getCondition()));
                } else if ($model->getData('notification_type') == Notification::REVIEW_REMINDERS) {
                    $model->setData('order_status_review', $this->serialize->unserialize($model->getCondition()));
                } else if ($model->getData('notification_type') == Notification::ABANDONED_CART_REMINDS) {
                    $model->setData('set_abandoned_cart_time', $model->getCondition());
                } else if ($model->getData('notification_type') == Notification::BIRTHDAY) {
                    $model->setData('set_notification_sent_before', $model->getCondition());
                } else if ($model->getData('notification_type') == Notification::CUSTOMER_LOGIN) {
                    $model->setData('set_time_after_login', $model->getCondition());
                } else if ($model->getData('notification_type') == Notification::MAINTENANCE) {
                    $model->setData('set_time_after_buy', $model->getCondition());
                } else {
                    $model->setData('custom_notification_type', $model->getCondition());
                }
            }

            if ($model->getSendTime() == 'send_after_the_trigger_condition') {
                try {
                    $schedule = $this->serialize->unserialize($model->getSchedule());
                } catch (Exception $e) {
                    $schedule = '';
                }
                if (isset($schedule['send_after'])) {
                    $model->setData('send_after', $schedule['send_after']);
                    $model->setData('unit', $schedule['unit']);
                }
            } elseif ($model->getSendTime() == 'schedule_time') {
                if ($model->getData('notification_type') == Notification::CUSTOM_TYPE) {
                    $model->setData('schedule_to', $model->getSchedule());
                } else {
                    $model->setData('schedule_to', $model->getScheduleTo());
                }
            }
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get('magenest_notification');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('magenest_notification');
        }

        return $this->loadedData;
    }
}
