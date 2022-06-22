<?php
namespace Magenest\SellOnInstagram\Model\ResourceModel;

use Exception;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magenest\SellOnInstagram\Model\InstagramFeedIndex as InstagramFeedIndexModel;

class InstagramFeedIndex extends AbstractDb
{
    /**
     * @var AdapterInterface
     */
    protected $_connection;

    public function _construct()
    {
        $this->_init(InstagramFeedIndexModel::TABLE, 'id');
        $this->_connection = $this->getConnection();
    }

    /**
     * @param $data
     * @throws Exception
     */
    public function insertData($data)
    {
        try {
            if (is_array($data)) {
                $this->_connection->insertOnDuplicate(
                    $this->getMainTable(),
                    $data,
                    ['feed_id', 'template_id', 'product_ids']
                );
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
