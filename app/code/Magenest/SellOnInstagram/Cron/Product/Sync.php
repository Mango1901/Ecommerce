<?php
namespace Magenest\SellOnInstagram\Cron\Product;

use Exception;
use Magenest\SellOnInstagram\Helper\Data;
use Magenest\SellOnInstagram\Logger\Logger;
use Magenest\SellOnInstagram\Model\History;
use Magenest\SellOnInstagram\Model\BatchBuilder;
use Magenest\SellOnInstagram\Model\ProductBatch;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed\CollectionFactory as FeedCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonFramework;
use Magenest\SellOnInstagram\Model\InstagramFeedFactory;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed as InstagramResourceModel;

class Sync
{
    const STATUS_ENABLE = 1;
    const PAGE_SIZE = 1500;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var ProductCollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var ProductBatch
     */
    protected $productBatch;
    /**
     * @var History
     */
    protected $history;
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var BatchBuilder
     */
    protected $batchBuilder;
    /**
     * @var NotifierInterface
     */
    protected $notifier;
    /**
     * @var TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var JsonFramework
     */
    protected $json;
    /**
     * @var FeedCollectionFactory
     */
    protected $feedCollectionFactory;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var InstagramFeedFactory
     */
    protected $feedFactory;
    /**
     * @var InstagramResourceModel
     */
    protected $feedResource;

    public function __construct(
        Logger $logger,
        Data $helper,
        ProductCollectionFactory $collectionFactory,
        ProductBatch $productBatch,
        History $history,
        ProductCollectionFactory $productCollectionFactory,
        BatchBuilder $batchBuilder,
        TimezoneInterface $localeDate,
        JsonFramework $json,
        FeedCollectionFactory $feedCollectionFactory,
        ResourceConnection $resourceConnection,
        InstagramFeedFactory $feedFactory,
        InstagramResourceModel $feedResource,
        NotifierInterface $notifier
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
        $this->productBatch = $productBatch;
        $this->history = $history;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->batchBuilder = $batchBuilder;
        $this->localeDate = $localeDate;
        $this->json = $json;
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->feedFactory = $feedFactory;
        $this->feedResource = $feedResource;
        $this->notifier = $notifier;
    }

    public function syncProduct()
    {
        try {
            $feedCollections = $this->feedCollectionFactory->create()
                ->addFieldToFilter('status', self::STATUS_ENABLE)
                ->addFieldToFilter('cron_enable', self::STATUS_ENABLE);
            foreach ($feedCollections as $feedCollection) {
                if ($this->validateTime($feedCollection->getCronDay(), $feedCollection->getCronTime())) {
                    $feedCollection->generateProduct();
                    $feedCollection->syncByFeedId();

                    $feedId = $feedCollection->getId();
                    $productIds = $this->getProductIds($feedId);

                    if (!empty($productIds)) {
                        $storeId = $feedCollection->getStoreId();
                        $templateId = $feedCollection->getTemplateId();
                        $this->addToHistoryDetail($productIds, $feedId);
                        $this->processSync($productIds, $templateId, $feedId, $storeId);
                    }
                }
            }
        } catch (Exception $exception) {
            $this->logger->error("Error Sync Product " . $exception->getMessage());
        }
    }

    protected function addToHistoryDetail($productIds, $feedId)
    {
        $productBatch = $this->productBatch->setCountItemsSuccess($productIds)->setAction(ProductBatch::CREATE);
        $history = $this->history->updateReport($productBatch, $feedId);
        $history->setLastId($history->getId());
    }

    protected function processSync($productIds, $templateId, $feedId, $storeId)
    {
        $pageSize = self::PAGE_SIZE;
        $productCollection = $this->productCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->setStoreId($storeId);
        $productCollectionSize = $productCollection->getSize();
        $pages = ceil($productCollectionSize / $pageSize);
        $productCollection->setPageSize($pageSize);
        for ($i = 1; $i <= $pages; $i++) {
            $requests = [];
            $productCollection->setCurPage($i);
            $productCollection->load();
            foreach ($productCollection as $collection) {
                $requests [] = $this->batchBuilder->requestCreateProductAction($collection, $templateId);
            }
            $data = $this->batchBuilder->createProductItemTemplate($this->helper->getAccessToken(), $requests);
            $this->productBatch->syncProductToFbShop($data, $feedId);
            $productCollection->clear();
        }
    }

    private function validateTime($cronDay, $cronTime)
    {
        $days = $this->json->unserialize($cronDay);
        $times = $this->json->unserialize($cronTime);

        $currentDay = date('w');
        if (in_array($currentDay, $days)) {
            $mageTime = $this->localeDate->scopeTimeStamp();
            $now = (date("H", $mageTime) * 60) + date("i", $mageTime);
            $modNow = intdiv($now, 30);
            if (in_array($modNow * 30, $times)) {
                return true;
            }
        }
        return false;
    }

    protected function getProductIds($feedId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            ['ifi' => $this->resourceConnection->getTableName('magenest_instagram_feed_index')],
            ['ifi.product_ids']
        )->where('feed_id = ?', $feedId);
        $productIds = $connection->fetchOne($select);

        return $productIds ? $this->json->unserialize($productIds) : [];
    }
}
