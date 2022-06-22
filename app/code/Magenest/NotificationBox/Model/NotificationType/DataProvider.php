<?php

namespace Magenest\NotificationBox\Model\NotificationType;

use Magenest\NotificationBox\Model\ResourceModel\NotificationType\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Serialize\Serializer\Json;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var DataPersistorInterface  */
    protected $dataPersistor;

    /** @var $loadedData */
    protected $loadedData;

    /** @var CollectionFactory */
    protected $collection;
    /** @var Json  */
    protected $serialize;

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
        Json $serialize,
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->serialize  = $serialize;
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
            if(isset($model->getData()['icon']))
            {
                try
                {
                    $model->setData('icon',$this->serialize->unserialize($model->getIcon()));
                }catch (\Exception $e){
                    $model->setData('icon','');
                }
            }
            $this->loadedData[$model->getEntityId()] = $model->getData();
        }
        return $this->loadedData;
    }
}
