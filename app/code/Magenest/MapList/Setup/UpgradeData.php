<?php

namespace Magenest\MapList\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * @var $eavSetup \Magento\Eav\Setup\EavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(array('setup' => $setup));

        $installer = $setup;

        $installer->startSetup();

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'stores');

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'stores',
            array(
                'type' => 'text',
                'label' => 'Stores',
                'input' => 'multiselect',
                'source' => 'Magenest\MapList\Model\Config\Source\Custom',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'backend' => 'Magenest\MapList\Model\Product\Attribute\Backend\Stores',
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
            )
        );

        $installer->endSetup();
    }
}
