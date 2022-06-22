<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 08/11/2021
 * Time: 10:02
 */

namespace Magenest\Core\Setup\Patch\Data;

use Magento\Catalog\Model\Config;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RemoveProductAttributes implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    private $attributeManagement;

    private $config;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AttributeManagementInterface $attributeManagement
     * @param Config $config
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        AttributeManagementInterface $attributeManagement,
        Config $config,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeManagement = $attributeManagement;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->removeAttribute('catalog_product', 'price_list_release_date');
        $eavSetup->removeAttribute('catalog_product', 'is_subscription');
        $eavSetup->removeAttribute('catalog_product', 'is_instalment');
        $eavSetup->removeAttribute('catalog_product', 'onesignal_new');
        $eavSetup->removeAttribute('catalog_product', 'stores');
        $eavSetup->removeAttribute('catalog_product', 'guarantee');
        $eavSetup->removeAttribute('catalog_product', 'guarantee_time');

    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}

