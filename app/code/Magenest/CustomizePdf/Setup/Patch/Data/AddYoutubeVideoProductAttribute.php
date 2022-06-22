<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 20/10/2021
 * Time: 11:44
 */

namespace Magenest\CustomizePdf\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddYoutubeVideoProductAttribute implements DataPatchInterface
{
    const YOUTUBE_VIDEO_IFRAME = "youtube_video_iframe";

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var EavSetupFactory
     */
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
        $config = [
            'type' => 'text',
            'label' => 'Product Youtube Video',
            'input' => 'textarea',
            'attribute_set' => 'Default',
            'backend' => '',
            'frontend' => '',
            'class' => '',
            'source' => '',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'default' => '',
            'filterable' => true,
            'searchable' => true,
            'comparable' => false,
            'unique' => false,
            'note' => 'Go to video product in youtube. Click to "Share" button in bottom right, choose "Embed" and click "Copy" button'
        ];

        if (!$eavSetup->getAttribute(Product::ENTITY, self::YOUTUBE_VIDEO_IFRAME)) {
            $eavSetup->addAttribute(Product::ENTITY, self::YOUTUBE_VIDEO_IFRAME, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, self::YOUTUBE_VIDEO_IFRAME, $config);
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
