<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 */

namespace Magenest\CustomCatalog\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AddIncludeInCategoryMenu
 */
class AddIncludeInCategoryMenu implements DataPatchInterface
{
    const ATTRIBUTE_CODE = 'include_in_category_menu';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Collection
     */
    private $collectionCategory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddCustomSorting constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param Collection $collectionCategory
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Collection $collectionCategory,
        ResourceConnection $resource,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->collectionCategory = $collectionCategory;
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(Category::ENTITY, self::ATTRIBUTE_CODE, [
            'type' => 'int',
            'label' => 'Include in Category Menu',
            'input' => 'select',
            'source' => Boolean::class,
            'default' => '1',
            'sort_order' => 10,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'General Information',
        ]);

        $bulkInsert = [];
        $attributeId = $eavSetup->getAttribute(Category::ENTITY, self::ATTRIBUTE_CODE, 'attribute_id');

        foreach ($this->collectionCategory->getAllIds() as $categoryId) {
            $bulkInsert[] = [
                'attribute_id' => (int)$attributeId,
                'store_id' => 0,
                'entity_id' => (int)$categoryId,
                'value' => 1
            ];
        }

        $this->insertMultiple('catalog_category_entity_int', $bulkInsert);
    }

    /**
     * @param $table
     * @param $data
     * @return int|void
     */
    private function insertMultiple($table, $data)
    {
        try {
            $tableName = $this->resource->getTableName($table);
            return $this->moduleDataSetup->getConnection()->insertMultiple($tableName, $data);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
