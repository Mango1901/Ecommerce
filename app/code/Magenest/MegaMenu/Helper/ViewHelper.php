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

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magenest\MegaMenu\Model\ResourceModel\MenuEntity\Collection;

class ViewHelper extends AbstractHelper
{
    const MEGAMENU_ASSET_DIR = 'mega_menu/item/';

    private $_groupCollection;

    private $_storeManager;

    private $menuEntityCollectionFactory;

    private $helper;

    private $classes;

    protected $menuItemFactory;

    /**
     * ViewHelper constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magenest\MegaMenu\Model\ResourceModel\MenuEntity\CollectionFactory $menuEntityCollectionFactory
     * @param Data $helper
     * @param \Magenest\MegaMenu\Model\Classes $classes
     * @param \Magenest\MegaMenu\Model\MenuEntityFactory $menuEntityFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\MegaMenu\Model\ResourceModel\MenuEntity\CollectionFactory $menuEntityCollectionFactory,
        \Magenest\MegaMenu\Helper\Data $helper,
        \Magenest\MegaMenu\Model\Classes $classes,
        \Magenest\MegaMenu\Model\MenuEntityFactory $menuEntityFactory
    ) {
        $this->menuItemFactory = $menuEntityFactory;
        $this->classes = $classes;
        $this->helper = $helper;
        $this->menuEntityCollectionFactory = $menuEntityCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_groupCollection = $collectionFactory;
        parent::__construct($context);
    }

    public function getMenuResultStructure($menuId, $items)
    {
        if ($items && count($items)) {
            return $this->getPreviewMenuModel($items);
        }

        return $this->getNonPreviewMenuModel($menuId);
    }

    /**
     * @param $menuId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getNonPreviewMenuModel($menuId)
    {
        $result = [];
        /** @var Collection $menuItemCollection */
        $menuItemCollection = $this->menuEntityCollectionFactory->create();
        $menuItemCollection->addAttributeToSelect('*')
            ->addAttributeToFilter('menu_id', ['eq' => $menuId])
            ->setOrder('sort', \Magento\Framework\DB\Select::SQL_ASC);

        $menuItemCollection->addAttributeToFilter('itemEnable', ['eq' => '1'])->addAttributeToFilter('level', [
            ['null' => true], ['eq' => '0']
        ], 'left');

        if ($menuItemCollection->getSize() > 0) {
            /** @var \Magenest\MegaMenu\Model\MenuEntity $menuItem */
            foreach ($menuItemCollection as $menuItem) {
                $menuItem = $this->menuItemFactory->create()->load($menuItem->getEntityId());
                if ($menuItem->getLevel() != '0') {
                    continue;
                }

                $menuItemData = $menuItem->getChildrenTreeFormat();
                $menuItemData['id'] = $menuItem->getEntityId();
                $menuItemData['title'] = $menuItem->getTitle();
                $menuItemData['is_new'] = 'false';
                if ($menuItemData['mainContentType'] == null) {
                    $menuItemData['mainContentType'] = 'grid';
                    if ($menuItemData['mainColumn'] == null) {
                        $menuItemData['mainColumn'] = 4;
                    }
                }

                if ($menuItemData['mainEnable'] == 1
                    && $menuItemData['mainContentHtml'] == null
                    && $menuItemData['leftContentHtml'] == null
                    && $menuItemData['rightContentHtml'] == null
                    && $menuItemData['hasChild'] == 'no'
                    && ($menuItemData['mainContentType'] == "sub-category" && empty($menuItemData['mainParentCategory']))) {
                    $menuItemData['mainEnable'] = 0;
                }
                $result['menu_item_nest'][] = $menuItemData;
            }
        }

        return $result;
    }

    /**
     * @param $items
     * @return array
     */
    protected function getPreviewMenuModel($items)
    {
        $result = [];
        foreach ($items as $itemData) {
            if ($itemData['level'] == '0' && $itemData['itemEnable'] == '1') {
                $this->helper->getItemData($itemData, $items);

                $rData = $itemData['data'] ?? "";
                $parsedData = json_decode($rData, true);
                if ($parsedData) {
                    $itemData = array_merge($itemData, $parsedData);
                }
                if ($itemData['icon'] ?? false) {
                    $itemData['icon'] = [
                        'name' => $itemData['icon'],
                        'previewType' => 'image',
                        'url' => $this->getMediaAssetUrl($itemData['icon'])
                    ];
                }
                $result['menu_item_nest'][] = $itemData;
            }
        }

        return $result;
    }

    public function getCustomerGroups()
    {
        $result = array();
        $customerGroups = $this->_groupCollection->create();

        foreach ($customerGroups as $group) {
            $result[] = array('value' => $group->getId(), 'label' => $group->getCode());
        }

        return $result;
    }

    public function getMediaAssetUrl($name)
    {
        return $this->getBaseMediaUrl() . self::MEGAMENU_ASSET_DIR . $name;
    }

    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
    }

    public function getClassesObject()
    {
        return $this->classes;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

}
