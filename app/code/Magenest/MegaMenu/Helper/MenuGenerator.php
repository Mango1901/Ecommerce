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

namespace Magenest\MegaMenu\Helper;

class MenuGenerator
{
    protected $_helper;

    protected $_menuResource;

    protected $_megaMenuFactory;

    protected $_menuEntityFactory;

    /**
     * MenuGenerator constructor.
     * @param Data $helper
     * @param \Magenest\MegaMenu\Model\MegaMenuFactory $megaMenuFactory
     * @param \Magenest\MegaMenu\Model\ResourceModel\MegaMenu $menuResource
     * @param \Magenest\MegaMenu\Model\MenuEntityFactory $menuEntityFactory
     */
    public function __construct(
        \Magenest\MegaMenu\Helper\Data $helper,
        \Magenest\MegaMenu\Model\MegaMenuFactory $megaMenuFactory,
        \Magenest\MegaMenu\Model\ResourceModel\MegaMenu $menuResource,
        \Magenest\MegaMenu\Model\MenuEntityFactory $menuEntityFactory
    ) {
        $this->_menuEntityFactory = $menuEntityFactory;
        $this->_megaMenuFactory = $megaMenuFactory;
        $this->_menuResource = $menuResource;
        $this->_helper = $helper;
    }

    public function generateGridMenuByCategories()
    {
        try {
            $menu = $this->createMegaMenu();
            $this->createMenuEntity($menu->getId());

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function createMegaMenu()
    {
        $menu = $this->_megaMenuFactory->create();
        $menu->addData([
            'menu_name' => __('Sample Grid Type Menu'),
            'store_id' => 0,
            'menu_template' => 'horizontal'
        ]);
        $menu->save();

        return $menu;
    }

    protected function createMenuEntity($menuId)
    {
        $categoryData = $this->prepareCategoryData();
        $entitiesOfCategory = [];
        $childrenOfCategory = [];
        foreach ($categoryData as $categoryDatum) {
            $menuEntity = $this->_menuEntityFactory->create()->addData([
                'menu_id' => $menuId,
                'title' => $categoryDatum['name'],
                'level' => $categoryDatum['level'] - 2,
                'children' => '',
                'position' => $categoryDatum['position'],
                'type' => 'cat',
                'link' => $categoryDatum['link'],
                'label' => $categoryDatum['name'],
                'mainEnable' => 1,
                'mainContentType' => 'grid',
                'mainColumn' => '4',
            ])->save();

            $entitiesOfCategory[$categoryDatum['entity_id']] = $menuEntity;

            if (!isset($childrenOfCategory[$categoryDatum['parent_id']])) {
                $childrenOfCategory[$categoryDatum['parent_id']] = [];
            }
            $childrenOfCategory[$categoryDatum['parent_id']][] = $categoryDatum['entity_id'];
        }
        foreach ($childrenOfCategory as $categoryId => $children) {
            if (!isset($entitiesOfCategory[$categoryId])) {
                continue;
            }
            $childEntities = [];
            foreach ($children as $child) {
                if (!isset($entitiesOfCategory[$child])) {
                    continue;
                }
                $childEntities[] = $entitiesOfCategory[$child]->getEntityId();
            }
            $entitiesOfCategory[$categoryId]->setChildren(implode(',', $childEntities))->save();
        }
        usort($entitiesOfCategory, function ($a, $b) {
            if ($a->getPosition() !== $b->getPosition()) {
                return $a->getPosition() - $b->getPosition();
            }

            return $a->getLevel() - $b->getLevel();
        });
        $sort = 0;
        foreach ($entitiesOfCategory as $entity) {
            $entity->setSort($sort++)->save();
        }
    }

    protected function prepareCategoryData()
    {
        return $this->_helper->getCategories();
    }
}
