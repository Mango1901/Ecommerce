<?php

namespace Magenest\NotificationBox\Ui\Component\NotificationType;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType\Collection;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    /** @var Collection  */
    protected $notificationCollection;

    /**
     * @param Collection $notificationCollection
     */
    public function __construct(Collection $notificationCollection)
    {
        $this->notificationCollection = $notificationCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $allNotificationType[] = [
            'label' => "-- select --", 'value' => ""
        ];
        $this->notificationCollection->getData();
        foreach ($this->notificationCollection->getData() as $item){
            if(isset($item['default_type']) && $item['default_type']!== 'null'){
                $item['entity_id'] = $item['default_type'];
            }
            $allNotificationType[] = ['label'=> $item['name'], 'value' => $item['entity_id']];
        }
        return $allNotificationType;
    }
}
