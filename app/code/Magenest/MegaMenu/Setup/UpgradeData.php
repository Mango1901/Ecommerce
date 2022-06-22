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

use Zend_Validate_Exception;
use Magenest\MegaMenu\Model\MenuEntity;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magenest\MegaMenu\Model\ResourceModel\Label;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 *
 * @package Magenest\MegaMenu\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var MenuEntitySetupFactory
     */
    private $menuEntitySetupFactory;

    /**
     * @var Label
     */
    private $labelResource;

    public function __construct(
        MenuEntitySetupFactory $menuEntitySetupFactory,
        Label $labelResourceModel
    ) {
        $this->menuEntitySetupFactory = $menuEntitySetupFactory;
        $this->labelResource = $labelResourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var MenuEntitySetup $menuEntitySetup */
        $menuEntitySetup = $this->menuEntitySetupFactory->create(['setup' => $setup]);
        $menuEntitySetup->installEntities();

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'footerEnable', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'footerClass', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'footerContentHtml', ['type' => 'text']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'headerEnable', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'headerContentHtml', ['type' => 'text']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'headerClass', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'color', ['type' => 'varchar']);
        }

        if (version_compare($context->getVersion(), '2.1.3') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'backgroundColor', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'backgroundWidth', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'backgroundHeight', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'backgroundOpacity', ['type' => 'varchar']);
        }

        if (version_compare($context->getVersion(), '2.1.5') < 0) {
            $this->upgradeAttribute($menuEntitySetup);
        }

        if (version_compare($context->getVersion(), '2.1.6') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'icon', ['type' => 'varchar']);
            $this->upgradeAttribute($menuEntitySetup);
        }

        if (version_compare($context->getVersion(), '2.1.7') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'background_image', ['type' => 'varchar']);
            $this->upgradeAttribute($menuEntitySetup);
        }

        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            $this->upgradeAttribute($menuEntitySetup);
        }

        if (version_compare($context->getVersion(), '2.2.1') < 0) {
            $this->addLabelAttribute($menuEntitySetup);
        }

        if (version_compare($context->getVersion(), '2.2.4') < 0) {
            $this->addSampleLabels();
        }
        if (version_compare($context->getVersion(), '2.2.8') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'itemEnable', ['type' => 'varchar']);
        }
        if (version_compare($context->getVersion(), '2.2.9') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'linkTarget', ['type' => 'varchar']);
        }
        if (version_compare($context->getVersion(), '2.2.10') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'cssInline', ['type' => 'varchar']);
        }
        if (version_compare($context->getVersion(), '2.2.11') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'hoverIconColor', ['type' => 'text']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'hideText', ['type' => 'varchar']);
        }

        if (version_compare($context->getVersion(), '2.2.12') < 0) {
            $this->updateMenuItemDropdownConfig($menuEntitySetup);
        }

        if (version_compare($context->getVersion(), '2.2.13')) {
            $this->addSubCategoryMainContent($menuEntitySetup);
        }

        if (version_compare($context->getVersion(), '2.2.20')) {
            $this->alterAnimationInMenuAttribute($menuEntitySetup);
        }
        if (version_compare($context->getVersion(), '2.2.21') < 0) {
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'backgroundRepeat', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'backgroundSize', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'animateDelayTime', ['type' => 'varchar']);
            $menuEntitySetup->addAttribute(
                MenuEntity::ENTITY, 'animateSpeed', ['type' => 'varchar']);
        }

        if (version_compare($context->getVersion(), "2.2.22", "<")) {
            $this->updateMenuTypeAttribute($menuEntitySetup);
        }

        $this->upgradeAttribute($menuEntitySetup);
        $setup->endSetup();
    }

    /**
     * Upgrade attribute
     *
     * @param MenuEntitySetup $menuEntitySetup
     */
    public function upgradeAttribute($menuEntitySetup)
    {
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'level', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'title', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'sort', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'parent_id', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'parent_id_temp', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'id_temp', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'brother_name', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'children', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'parent_id_temp', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'is_active', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'is_mega', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'megaColumn', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'type', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'link', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'obj_id', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'class', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'cssClass', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'showIcon', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'leftEnable', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'leftClass', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'leftWidth', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'leftContentHtml', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'mainEnable', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'mainProductSku', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'mainInCategory', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'mainContentType', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'mainColumn', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'mainContentHtml', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'rightEnable', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'rightClass', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'rightWidth', 'backend_type', 'int');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'rightContentHtml', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'textColor', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'hoverTextColor', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'footerEnable', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'footerClass', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'footerContentHtml', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'headerEnable', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'headerContentHtml', 'backend_type', 'text');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'headerClass', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'color', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'backgroundColor', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'backgroundWidth', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'backgroundHeight', 'backend_type', 'varchar');
        $menuEntitySetup->updateAttribute(
            MenuEntity::ENTITY, 'backgroundOpacity', 'backend_type', 'varchar');
    }

    /**
     * @param MenuEntitySetup $menuEntitySetup
     */
    private function addLabelAttribute($menuEntitySetup)
    {
        $menuEntitySetup->addAttribute(
            MenuEntity::ENTITY, 'label', ['type' => 'int']);
    }

    private function addSampleLabels()
    {
        $connection = $this->labelResource->getConnection();
        $connection->beginTransaction();
        $connection->insertMultiple(
            $this->labelResource->getTable('magenest_menu_label'), [
                                                                     [
                                                                         'name' => 'Hot Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label arrow label-position-top-right" style="color: rgb(255, 255, 255); background-color: rgb(255, 0, 0); border-radius: 4px; font-size: 10px; width: 38px; padding: 0px; height: 17px; line-height: 17px;"><span class="label-text">Hot</span><span class="label-arrow" style="border-color: rgb(255, 0, 0); border-width: 3px;"></span></label>',
                                                                         'label_text' => 'Hot',
                                                                         'label_position' => 'label-position-top-right'
                                                                     ],
                                                                     [
                                                                         'name' => 'New Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label arrow label-position-top-right" style="color: rgb(255, 255, 255); background-color: rgb(0, 143, 33); border-radius: 4px; width: 38px; padding: 0px; font-size: 10px; height: 17px; line-height: 17px;"><span class="label-text">New</span><span class="label-arrow" style="border-color: rgb(0, 143, 33); border-width: 3px;"></span></label>',
                                                                         'label_text' => 'New',
                                                                         'label_position' => 'label-position-top-right'
                                                                     ],
                                                                     [
                                                                         'name' => 'Sale Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label arrow label-position-top-right" style="font-size: 10px; width: 38px; padding: 0px; height: 17px; line-height: 17px; color: rgb(255, 255, 255); background-color: rgb(235, 204, 0); border-radius: 4px;"><span class="label-text">Sale</span><span class="label-arrow" style="border-width: 3px; border-color: rgb(235, 204, 0);"></span></label>',
                                                                         'label_text' => 'Sale',
                                                                         'label_position' => 'label-position-top-right'
                                                                     ],
                                                                     [
                                                                         'name' => 'Trending Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label arrow label-position-top-right" style="font-size: 10px; width: 60px; padding: 0px; height: 17px; line-height: 17px; color: rgb(255, 255, 255); border-style: none; background-color: rgb(84, 167, 255); border-radius: 4px;"><span class="label-text">Trending</span><span class="label-arrow" style="border-width: 3px; border-color: rgb(84, 167, 255);"></span></label>',
                                                                         'label_text' => 'Trending',
                                                                         'label_position' => 'label-position-top-right'
                                                                     ],
                                                                     [
                                                                         'name' => 'Hot Light Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label label-position-top" style="font-size: 10px; width: 38px; padding: 0px; height: 17px; line-height: 17px; color: rgb(255, 0, 0); border-style: dashed; border-width: 1px; border-color: rgb(255, 0, 0);"><span class="label-text">Hot</span><span class="label-arrow" style=""></span></label>',
                                                                         'label_text' => 'Hot',
                                                                         'label_position' => 'label-position-top'
                                                                     ],
                                                                     [
                                                                         'name' => 'New Light Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label label-position-top" style="font-size: 10px; width: 38px; padding: 0px; height: 17px; line-height: 17px; color: rgb(0, 143, 33); border-color: rgb(0, 143, 33); border-style: dashed; border-width: 1px;"><span class="label-text">New</span><span class="label-arrow"></span></label>',
                                                                         'label_text' => 'New',
                                                                         'label_position' => 'label-position-top'
                                                                     ],
                                                                     [
                                                                         'name' => 'Sale Light Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label label-position-top" style="font-size: 10px; width: 38px; padding: 0px; height: 17px; line-height: 17px; color: rgb(201, 191, 0); border-width: 1px; border-style: dashed; border-color: rgb(201, 191, 0);"><span class="label-text">Sale</span><span class="label-arrow"></span></label>',
                                                                         'label_text' => 'Sale',
                                                                         'label_position' => 'label-position-top'
                                                                     ],
                                                                     [
                                                                         'name' => 'Trending Light Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label label-position-top" style="color: rgb(84, 167, 255); border-style: dashed; border-color: rgb(84, 167, 255); border-width: 1px; font-size: 10px; width: 60px; padding: 0px; height: 17px; line-height: 17px;"><span class="label-text">Trending</span><span class="label-arrow"></span></label>',
                                                                         'label_text' => 'Trending',
                                                                         'label_position' => 'label-position-top'
                                                                     ],
                                                                     [
                                                                         'name' => 'Hot Text Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label label-position-top-left" style="font-size: 10px; width: auto; padding: 1px 5px; height: auto; color: rgb(255, 0, 0); border-style: none; background-color: rgb(255, 219, 219); border-radius: 0px;"><span class="label-text">Hot</span><span class="label-arrow" style=""></span></label>',
                                                                         'label_text' => 'Hot',
                                                                         'label_position' => 'label-position-top-left'
                                                                     ],
                                                                     [
                                                                         'name' => 'New Text Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label label-position-top-left" style="font-size: 10px; width: auto; padding: 1px 5px; height: auto; color: rgb(0, 143, 33); background-color: rgb(200, 240, 208);"><span class="label-text">New</span><span class="label-arrow"></span></label>',
                                                                         'label_text' => 'New',
                                                                         'label_position' => 'label-position-top-left'
                                                                     ],
                                                                     [
                                                                         'name' => 'Sale Text Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label label-position-top-left" style="font-size: 10px; color: rgb(194, 139, 0); background-color: rgb(255, 245, 181);"><span class="label-text">Sale</span><span class="label-arrow"></span></label>',
                                                                         'label_text' => 'Sale',
                                                                         'label_position' => 'label-position-top-left'
                                                                     ],
                                                                     [
                                                                         'name' => 'Trending Text Label',
                                                                         'to_html' => '<label id="megamenu-label" class="megamenu-label label-position-top-left" style="color: rgb(84, 167, 255); background-color: rgb(207, 231, 255);"><span class="label-text">Trending</span><span class="label-arrow"></span></label>',
                                                                         'label_text' => 'Trending',
                                                                         'label_position' => 'label-position-top-left'
                                                                     ]
                                                                 ]);
        $connection->commit();
    }

    /**
     * @param MenuEntitySetup $menuEntitySetup
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    private function updateMenuItemDropdownConfig(MenuEntitySetup $menuEntitySetup)
    {
        $menuEntitySetup->addAttribute(MenuEntity::ENTITY, 'animation_in', ['type' => 'int', 'label' => "Animation In"]);
        $menuEntitySetup->addAttribute(MenuEntity::ENTITY, 'animation_time', ['type' => 'int', 'label' => "Animation Time"]);
        $menuEntitySetup->addAttribute(MenuEntity::ENTITY, 'background_position_x', ['type' => 'decimal', 'label' => "Background Position X"]);
        $menuEntitySetup->addAttribute(MenuEntity::ENTITY, 'background_position_y', ['type' => 'decimal', 'label' => "Background Position Y"]);
        $menuEntitySetup->addAttribute(MenuEntity::ENTITY, 'main_content_width', ['type' => 'decimal', 'label' => "Main Content Width"]);
    }

    private function addSubCategoryMainContent(MenuEntitySetup $menuEntitySetup)
    {
        $menuEntitySetup->addAttribute(MenuEntity::ENTITY, 'parent_category_content', ['type' => 'int', 'label' => "Parent Category"]);
    }

    private function alterAnimationInMenuAttribute(MenuEntitySetup $menuEntitySetup)
    {
        $menuEntitySetup->removeAttribute(MenuEntity::ENTITY, 'animation_in');
        $menuEntitySetup->addAttribute(MenuEntity::ENTITY, 'animation_in', ['type' => 'varchar', 'label' => "Animation In"]);
    }

    /**
     * @param MenuEntitySetup $menuEntitySetup
     */
    private function updateMenuTypeAttribute(MenuEntitySetup $menuEntitySetup)
    {
        $menuEntitySetup->updateAttribute(MenuEntity::ENTITY, 'menu_type', ['frontend_label' => 'Menu Item Type']);
    }
}
