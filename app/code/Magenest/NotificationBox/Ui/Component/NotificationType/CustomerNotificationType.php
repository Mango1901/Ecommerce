<?php
namespace Magenest\NotificationBox\Ui\Component\NotificationType;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType\Collection;

class CustomerNotificationType implements \Magento\Framework\Option\ArrayInterface
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
            if($item['default_type'] != 'null' ){
                $allNotificationType[] = ['label'=> $item['name'], 'value' => $item['default_type']];
            }else{
                $allNotificationType[] = ['label'=> $item['name'], 'value' => $item['entity_id']];
            }
        }
        return $allNotificationType;
    }
}
