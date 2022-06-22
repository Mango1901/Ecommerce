<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\ContentHierarchy;

use Exception;
use Firebear\ImportExport\Helper\Data as FirebearHelper;
use Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\VersionsCms\Api\Data\HierarchyNodeInterface;
use Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node;

/**
 * Class AttributeCollection
 *
 * @package Firebear\ImportExport\Model\Export\ContentHierarchy
 */
class AttributeCollection extends Collection
{
    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var FirebearHelper
     */
    private $helper;

    /**
     * AttributeCollection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param AttributeFactory $attributeFactory
     * @param FirebearHelper $helper
     * @param array $data
     * @throws LocalizedException
     * @throws Exception
     */
    public function __construct(
        EntityFactory $entityFactory,
        AttributeFactory $attributeFactory,
        FirebearHelper $helper,
        array $data = []
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->node = $data['node'];
        $this->helper = $helper;
        parent::__construct($entityFactory);

        $tableNameMap = [
            $this->node->getMainTable() => 'main_table',
            $this->node->getTable('magento_versionscms_hierarchy_metadata') => 'metadata_table',
        ];

        $tableFields = $this->getAllTableFields();
        foreach ($tableFields as $key => $field) {
            $attributeData = [
                AttributeInterface::ATTRIBUTE_ID => $key,
                AttributeInterface::ATTRIBUTE_CODE => $field['COLUMN_NAME'],
                AttributeInterface::FRONTEND_LABEL => ucwords(str_replace('_', ' ', $field['COLUMN_NAME'])),
                AttributeInterface::BACKEND_TYPE => $this->helper->convertTypesTables($field['DATA_TYPE']),
                AttributeInterface::FRONTEND_INPUT => $this->helper->convertTypesTables($field['DATA_TYPE']),
                'backend_table_name' => $tableNameMap[$field['TABLE_NAME']],
            ];

            switch ($key) {
                case HierarchyNodeInterface::SCOPE:
                    $attributeData[AttributeInterface::FRONTEND_INPUT] = 'select';
                    $attributeData[AttributeInterface::SOURCE_MODEL] = AttributeSources\Scope::class;
                    break;
                case 'menu_layout':
                    $attributeData[AttributeInterface::FRONTEND_INPUT] = 'select';
                    $attributeData[AttributeInterface::SOURCE_MODEL] = AttributeSources\MenuLayout::class;
                    break;
                case 'menu_ordered':
                    $attributeData[AttributeInterface::FRONTEND_INPUT] = 'select';
                    $attributeData[AttributeInterface::SOURCE_MODEL] = AttributeSources\MenuListtype::class;
                    break;
                case 'menu_list_type':
                    $attributeData[AttributeInterface::FRONTEND_INPUT] = 'select';
                    $attributeData[AttributeInterface::SOURCE_MODEL] = AttributeSources\MenuListmode::class;
                    break;
                case 'pager_visibility':
                    $attributeData[AttributeInterface::FRONTEND_INPUT] = 'select';
                    $attributeData[AttributeInterface::SOURCE_MODEL] = AttributeSources\Visibility::class;
                    break;
                case 'menu_brief':
                    $attributeData[AttributeInterface::FRONTEND_INPUT] = 'select';
                    $attributeData[AttributeInterface::SOURCE_MODEL] = AttributeSources\MenuBrief::class;
                    break;
                case 'meta_first_last':
                case 'meta_next_previous':
                case 'meta_chapter':
                case 'meta_section':
                case 'meta_cs_enabled':
                case 'menu_visibility':
                case 'menu_excluded':
                case 'top_menu_excluded':
                case 'top_menu_visibility':
                    $attributeData[AttributeInterface::FRONTEND_INPUT] = 'select';
                    $attributeData[AttributeInterface::SOURCE_MODEL] = Boolean::class;
                    break;
            }

            $this->addItem(
                $this->attributeFactory->createAttribute(Attribute::class, $attributeData)
            );
        }
    }

    /**
     * Retrieve All Fields Source (the column descriptions for a table)
     *
     * @return array
     * @throws LocalizedException
     */
    private function getAllTableFields()
    {
        $connection = $this->node->getConnection();
        $mainTable = $this->node->getMainTable();
        $fields = $connection->describeTable($mainTable);
        $metadataTable = $this->node->getTable('magento_versionscms_hierarchy_metadata');
        $fields += $connection->describeTable($metadataTable);

        return $fields;
    }
}
