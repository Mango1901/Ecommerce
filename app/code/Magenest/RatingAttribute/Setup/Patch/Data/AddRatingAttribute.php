<?php
namespace Magenest\RatingAttribute\Setup\Patch\Data;

use Magenest\RatingAttribute\Model\Constant;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddRatingAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $setup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $this->addRatingAttribute($eavSetup);
    }

    /**
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addRatingAttribute(EavSetup $eavSetup)
    {
        $config = [
            'type' => 'int',
            'label' => 'Average Rating',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'sort_order' => 200,
            'used_for_sort_by' => true,
            'unique' => false
        ];
        if (!$eavSetup->getAttribute(Product::ENTITY, Constant::RATING_ATTRIBUTE)) {
            $eavSetup->addAttribute(Product::ENTITY, Constant::RATING_ATTRIBUTE, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, Constant::RATING_ATTRIBUTE, $config);
        }
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
}
