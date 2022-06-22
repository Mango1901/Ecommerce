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

namespace Magenest\MegaMenu\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $menuEntitySetupFactory;

    public function __construct(
        \Magenest\MegaMenu\Setup\MenuEntitySetupFactory $menuEntitySetupFactory
    ) {

        $this->menuEntitySetupFactory = $menuEntitySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $menuEntityEntity = \Magenest\MegaMenu\Model\MenuEntity::ENTITY;
        $menuEntitySetup = $this->menuEntitySetupFactory->create(array('setup' => $setup));
        $menuEntitySetup->installEntities();
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'title',
            array('type' => 'varchar')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'level',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'sort',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'parent_id',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'parent_id_temp',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'id_temp',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'brother_name',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'children',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'parent_id_temp',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'is_active',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'is_mega',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'megaColumn',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'type',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'link',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'obj_id',
            array('type' => 'int')
        );

        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'obj_id',
            array('type' => 'int')
        );

        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'class',
            array('type' => 'varchar')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'cssClass',
            array('type' => 'varchar')
        );
        //new
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'showIcon',
            array('type' => 'varchar')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'leftEnable',
            array('type' => 'varchar')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'leftClass',
            array('type' => 'varchar')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'leftWidth',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'leftContentHtml',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'mainEnable',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'mainProductSku',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'mainInCategory',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'mainContentType',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'mainColumn',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'mainContentHtml',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'rightEnable',
            array('type' => 'varchar')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'rightClass',
            array('type' => 'varchar')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'rightWidth',
            array('type' => 'int')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'rightContentHtml',
            array('type' => 'text')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'textColor',
            array('type' => 'varchar')
        );
        $menuEntitySetup->addAttribute(
            $menuEntityEntity,
            'hoverTextColor',
            array('type' => 'text')
        );

        //
        $setup->endSetup();
    }
}
