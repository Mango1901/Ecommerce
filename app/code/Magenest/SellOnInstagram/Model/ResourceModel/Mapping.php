<?php

namespace Magenest\SellOnInstagram\Model\ResourceModel;

use Exception;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Mapping
 * @package Magenest\SellOnInstagram\Model\ResourceModel
 */
class Mapping extends AbstractDb
{
    const TABLE = 'magenest_instagram_mapping_template';
    const TABLE_MAP = 'magenest_instagram_mapping';

    /**
     * @var null
     */
    protected $_feedMapTable = null;

    /**
     * @var AdapterInterface
     */
    protected $_connection;

    public function _construct()
    {
        $this->_init(self::TABLE, 'id');
        $this->_connection = $this->getConnection();
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getFieldsByEntity($type)
    {
        $select = $this->_connection->select()->from($this->getTable('eav_attribute'))->where('entity_type_id = ?', $type);

        return $this->_connection->fetchAll($select);
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getEavIdByType($type)
    {
        $select = $this->_connection->select()->from($this->getTable('eav_entity_type'))->where('entity_type_code = ?', $type);

        return $this->_connection->fetchRow($select);
    }

    /**
     * @param $data
     *
     * @throws DuplicateException
     */
    public function saveTemplateContent($data)
    {
        try {
            if (is_array($data) && !empty($data)) {
                $this->_connection->insertOnDuplicate(
                    $this->getFeedMapTable(), $data, array_keys($data)
                );
            }
        } catch (DuplicateException $duplicateException) {
            throw new DuplicateException(__("DUPLICATE: magento attribute fields is duplicated. We auto replace them."));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return string
     */
    private function getFeedMapTable()
    {
        if ($this->_feedMapTable === null) {
            $this->_feedMapTable = $this->getTable(self::TABLE_MAP);
        }

        return $this->_feedMapTable;
    }

    /**
     * @param $id
     * @param $array
     *
     * @throws Exception
     */
    public function deleteTemplateContent($id, $array)
    {
        try {
            $this->_connection->delete(
                $this->getFeedMapTable(), [
                    'magento_attribute IN (?)' => $array,
                    'template_id' => $id
                ]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    /**
     * @param $id
     * @param $array
     *
     * @throws Exception
     */
    public function deleteFbTemplateContent($id, $array)
    {
        try {
            $this->_connection->delete(
                $this->getFeedMapTable(), [
                    'fb_attribute IN (?)' => $array,
                    'template_id' => $id
                ]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return array
     * @throws Exception
     */
    public function getAllFieldMap($id)
    {
        try {
            $select = $this->_connection->select()->from($this->getFeedMapTable())->where('template_id = :template_id')->where('status = 1');

            return $this->_connection->fetchAll($select, [':template_id' => $id]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return array
     * @throws Exception
     */
    public function getAllMagentoMappedFields($id)
    {
        try {
            $select = $this->_connection->select()->from($this->getFeedMapTable(), ['magento_attribute'])->where('template_id = :template_id');

            return $this->_connection->fetchCol($select, [':template_id' => $id]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getNameById($id)
    {
        try {
            $select = $this->_connection->select()->from($this->getMainTable(), 'name')->where('id = :id');

            return $this->_connection->fetchOne($select, [':id' => $id]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
