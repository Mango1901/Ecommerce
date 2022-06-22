<?php

namespace Magenest\SellOnInstagram\Ui\DataProvider;

use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed\CollectionFactory as InstagramFeedCollectionFactory;

class FeedDataProvider extends ModifierPoolDataProvider
{

    /**
     * @var InstagramFeedCollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var array
     */
    protected $loadedData;

    public function __construct(
        InstagramFeedCollectionFactory $fbFeedCollectionFactory,
        DataPersistorInterface $dataPersistor,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    )
    {
        $this->collection = $fbFeedCollectionFactory->create();
        $this->collectionFactory = $fbFeedCollectionFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->loadedData[$item->getId()]['general'] = $item->getData();
        }

        return $this->loadedData;
    }
}
