<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kootoro extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kootoro
 */

namespace Magenest\Directory\Setup\Patch\Data;

use Magento\Eav\Model\AttributeRepository;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;

class AddAddressAttributes implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeRepository $attributeRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $this->addAddressAttributes($customerSetup, $this->moduleDataSetup);

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @param $customerSetup
     * @param $setup
     */
    public function addAddressAttributes($customerSetup, $setup)
    {
        $customerSetup->addAttribute('customer_address', 'city_id', [
            'type' => 'static',
            'label' => 'City',
            'input' => 'text',
            'position' => 85,
            'visible' => true,
            'visible_on_front' => true,
            'user_defined' => false,
            'required' => false,
            'system' => false,
        ]);
        $customerSetup->addAttribute('customer_address', 'district', [
            'type' => 'static',
            'label' => 'District',
            'input' => 'text',
            'position' => 86,
            'visible' => true,
            'visible_on_front' => true,
            'user_defined' => false,
            'backend_type' => 'static',
            'system' => false,
        ]);
        $customerSetup->addAttribute('customer_address', 'district_id', [
            'type' => 'static',
            'label' => 'District',
            'input' => 'text',
            'position' => 87,
            'visible' => true,
            'visible_on_front' => true,
            'user_defined' => false,
            'required' => false,
            'backend_type' => 'static',
            'system' => false,
        ]);
        $customerSetup->addAttribute('customer_address', 'ward', [
            'type' => 'static',
            'label' => 'Ward',
            'input' => 'text',
            'position' => 88,
            'visible' => true,
            'visible_on_front' => true,
            'backend_type' => 'static',
            'user_defined' => false,
            'system' => false,
        ]);
        $customerSetup->addAttribute('customer_address', 'ward_id', [
            'type' => 'static',
            'label' => 'Ward',
            'input' => 'text',
            'position' => 89,
            'visible' => true,
            'visible_on_front' => true,
            'user_defined' => false,
            'backend_type' => 'static',
            'required' => false,
            'system' => false,
        ]);

        $attributeCodes = ['city_id', 'district', 'district_id', 'ward', 'ward_id'];
        $tableNames = ['customer_address_entity', 'quote_address', 'sales_order_address'];
        $formCodes = ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address'];

        $data = [];
        foreach ($attributeCodes as $attributeCode) {
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', $attributeCode);
            $attribute->setData(
                'used_in_forms',
                $formCodes
            );
            if ($attributeCode == 'district' || $attributeCode == 'ward'){
                $attribute->setData(
                    'is_required',
                    false
                );
            }
            $this->attributeRepository->save($attribute);
            $attributeId = $attribute->getAttributeId();
            foreach ($formCodes as $formCode) {
                $data[] = ['form_code' => $formCode, 'attribute_id' => $attributeId];
            }
        }
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $setup->getConnection();
        $connection->insertOnDuplicate(
            $setup->getTable('customer_form_attribute'),
            $data,
            ['form_code', 'attribute_id']
        );

//        foreach ($tableNames as $tableName) {
//            $connection->addColumn($setup->getTable($tableName), 'city_id', ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => null, 'length' => 255, 'comment' => 'City Id',]);
//            $connection->addColumn($setup->getTable($tableName), 'district', ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => null, 'length' => 255, 'comment' => 'District']);
//            $connection->addColumn($setup->getTable($tableName), 'district_id', ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => null, 'length' => 255, 'comment' => 'District Id',]);
//            $connection->addColumn($setup->getTable($tableName), 'ward', ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => null, 'length' => 255, 'comment' => 'Ward']);
//            $connection->addColumn($setup->getTable($tableName), 'ward_id', ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => null, 'length' => 255, 'comment' => 'Ward Id']);
//        }
    }
}
