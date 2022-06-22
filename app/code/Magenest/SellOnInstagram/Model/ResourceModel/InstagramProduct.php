<?php


namespace Magenest\SellOnInstagram\Model\ResourceModel;

use Exception;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Serialize\Serializer\Json as JsonFramework;
use Magenest\SellOnInstagram\Model\InstagramProduct as InstagramProductModel;

class InstagramProduct extends AbstractDb
{

    /**
     * @var JsonFramework
     */
    protected $jsonFramework;
    /**
     * @var false|AdapterInterface
     */
    protected $_connection;

    public function __construct(
        JsonFramework $jsonFramework,
        Context $context,
        $connectionName = null
    ) {
        $this->jsonFramework = $jsonFramework;
        parent::__construct($context, $connectionName);
    }

    public function _construct()
    {
        $this->_init(InstagramProductModel::TABLE, 'id');
        $this->_connection = $this->getConnection();
    }

    /**
     * @param $productData
     * @param $feedId
     *
     * @throws Exception
     */
    public function saveFacebookProductStatus($productData, $feedId)
    {
        if (is_array($productData) && !empty($productData)) {
            $this->saveProduct($productData, $feedId);
        }
    }

    /**
     * @param $productData
     * @param $feedId
     *
     * @throws Exception
     */
    private function saveProduct($productData, $feedId)
    {
        try {
            $data = [];
            foreach ($productData as $item) {
                $data[] = [
                    'object_id' => $item['entity_id'],
                    'sku' => $item['sku'],
                    'feed_id' => $feedId
                ];
            }
            $this->insertMultipleData($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $feedId
     * @throws Exception
     */
    public function deleteOldSku($feedId)
    {
        try {
            $where = ['feed_id = ?' => $feedId];
            $this->_connection->delete($this->getMainTable(), $where);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param array $data
     *
     * @throws Exception
     */
    public function insertMultipleData($data = [])
    {
        try {
            if (is_array($data) && !empty($data)) {
                $this->_connection->insertMultiple($this->getMainTable(), $data);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
