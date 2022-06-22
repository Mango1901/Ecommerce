<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */
namespace Magenest\MegaMenu\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magenest\MegaMenu\Model\ResourceModel\MenuEntity\CollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class MegaMenu
 * @package Magenest\MegaMenu\Model\ResourceModel
 */
class MegaMenu extends AbstractDb
{
    /** @var CollectionFactory */
    protected $menuEntityCollection;

	protected $_idFieldName = 'menu_id';

    protected $helper;

    protected $serializer;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param \Magenest\MegaMenu\Helper\Data $helper
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        \Magenest\MegaMenu\Helper\Data $helper,
        SerializerInterface $serializer,
        $connectionName = null
    ) {
        $this->helper = $helper;
        $this->menuEntityCollection = $collectionFactory;
        $this->serializer = $serializer;
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('magenest_mega_menu', 'menu_id');
    }

    /**
     * @inheritdoc
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $menuEntities = $this->getEntityCollection($object);

        foreach ($menuEntities as $menuEntity) {
            $menuEntity->delete();
        }

        return $this;
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->helper->isBackupEnable() && !$object->isObjectNew()) {
            $data = $object->getData();
            $childMenu = $object->getChildMenus();
            $childMenu = base64_encode(gzcompress($childMenu, 9));
            unset($data['child_menus']);
            $structure = base64_encode($this->serializer->serialize($data));

            $logTable = $this->getTable('magenest_mega_menu_log');
            $connection = $this->getConnection();

            $select = $connection->select()->from($logTable, ['log_id', 'menu_id', 'version'])
                ->where('menu_id = ?', intval($object->getId()))
                ->order('version DESC');
            $result = $connection->fetchAll($select);
            usort($result, function ($a, $b) {
                return (int)$b['version'] - (int)$a['version'];
            });

            $backupVersion = (int)$this->helper->getNumberOfBackup();
            $count = count($result);
            if ($count > $backupVersion) {
                $deleteIds = [];
                foreach ($result as $k => $v) {
                    if ($k >= ($backupVersion - 1)) {
                        $deleteIds[] = $v['log_id'];
                        $where = ['log_id = (?)' => $v['log_id']];
                        $this->getConnection()->delete($logTable, $where);
                    }
                }
            }

            $menuData = [
                'menu_id' => $object->getId(),
                'version' => (int)$object->getCurrentVersion(),
                'menu_data' => $structure,
                'menu_structure' => $childMenu,
                'note' => $object->getMenuName(),
                'update_time' => time()
            ];
            $this->getConnection()->insert($logTable, $menuData);
        }

        return $this;
    }

    /**
     * Get Entity Collection
     *
     * @param AbstractModel|null $object
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityCollection(AbstractModel $object = null)
    {
        return $this->menuEntityCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('menu_id', ['eq' => $object->getId()])
            ->load();
    }

    public function getIdByAlias($alias, $storeId = 0)
    {
        $select = $this->getConnection()->select();
        $select->from($this->getMainTable(), 'menu_id');
        $select->where("menu_alias = :menu_alias");
        if($storeId){
            $select->where("store_id = ".$storeId);
        }
        $select->limit(1);

        return $this->getConnection()->fetchOne($select, ['menu_alias' => $alias]);
    }

    /**
     * Check if menu alisa exists
     *
     * @param \Magento\Framework\Model\AbstractModel $user
     * @return array
     */
    public function menuAliasExists(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select();

        $binds = [
            'menu_alias' => $object->getMenuAlias(),
            'store_id' => $object->getStoreId(),
            'menu_id' => (int)$object->getId(),
        ];

        $select->from(
            $this->getTable('magenest_mega_menu')
        )->where(
            '(menu_alias = :menu_alias AND store_id = :store_id)'
        )->where(
            'menu_id <> :menu_id'
        );

        return $connection->fetchRow($select, $binds);
    }

    /**
     * Whether a user's identity is confirmed
     *
     * @param \Magento\Framework\Model\AbstractModel $user
     * @return bool
     */
    public function isMenuAliasUnique(\Magento\Framework\Model\AbstractModel $object)
    {
        return !$this->menuAliasExists($object);
    }

    /**
     * Add validation rules to be applied before saving an entity
     *
     * @return \Zend_Validate_Interface $validator
     */
    public function getValidationRulesBeforeSave()
    {
        $menuIdentity = new \Zend_Validate_Callback([$this, 'isMenuAliasUnique']);
        $menuIdentity->setMessage(
            __('A menu with the same menu alias or store view already exists.'),
            \Zend_Validate_Callback::INVALID_VALUE
        );

        return $menuIdentity;
    }

}
