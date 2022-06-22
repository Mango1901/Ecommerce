<?php
namespace Magenest\SellOnInstagram\Model\ResourceModel;

use Exception;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magenest\SellOnInstagram\Model\InstagramFeedIndex;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramProduct as InstagramProductResourceModel;

class InstagramFeed extends AbstractDb
{
    const TABLE_CATALOG_PRODUCT = 'catalog_product_entity';
    /**
     * @var InstagramProduct
     */
    protected $instagramProduct;
    /**
     * @var SerializerJson
     */
    protected $serializerJson;

    public function __construct(
        InstagramProductResourceModel $instagramProduct,
        SerializerJson $serializerJson,
        Context $context,
        $connectionName = null
    ) {
        $this->instagramProduct = $instagramProduct;
        $this->serializerJson = $serializerJson;
        parent::__construct($context, $connectionName);
    }

    public function _construct()
    {
        $this->_init('magenest_instagram_feed', 'id');
    }

    /**
     * @param $feedId
     *
     * @throws Exception
     */
    public function syncByFeedId($feedId)
    {
        $this->instagramProduct->deleteOldSku($feedId);

        $productIds = $this->getProductIds($feedId);
        if (!empty($productIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                ['cpe' => $this->getTable(self::TABLE_CATALOG_PRODUCT)],
                ['cpe.sku', 'cpe.entity_id']
            )->where('entity_id IN (?)', $productIds);
            $productData = $connection->fetchAll($select);
            $this->instagramProduct->saveFacebookProductStatus($productData, $feedId);
        }
    }

    /**
     * @param $feedId
     *
     * @return array|null
     */
    public function getProductIds($feedId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['ifi' => $this->getTable(InstagramFeedIndex::TABLE)],
            ['ifi.product_ids']
        )->where('feed_id = ?', $feedId);
        $productIds = $connection->fetchOne($select);

        return $productIds ? $this->serializerJson->unserialize($productIds) : [];
    }
}
