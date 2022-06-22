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

use Magento\Catalog\Model\Category;
use Magenest\MegaMenu\Model\MegaMenu;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Data\Form\Element\Editor;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Data
 * @package Magenest\MegaMenu\Helper
 */
class Data extends AbstractHelper
{
    const CACHE_TYPES = ['config', 'full_page'];
    const EFFECT_PATH = 'mega_menu/general/effect';
    const ENABLE_PATH = 'mega_menu/general/enable';
    const BACKUP_ENABLE_PATH = 'mega_menu/backup/enable';
    const NUMBER_BACKUP_VERSION = 'mega_menu/backup/backup_times';

    const HOV_BTN_BG_COLOR_PATH = 'mega_menu/default_conf/hov_btn_bg_color';

    /**
     * @var \Magenest\MegaMenu\Model\MegaMenuFactory
     */
    protected $_menuFactory;

    /**
     * @var \Magenest\MegaMenu\Model\MenuEntityFactory
     */
    protected $_menuEntityFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $cacheFrontendPool;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magenest\MegaMenu\Model\PreviewFactory
     */
    protected $_previewFactory;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magenest\MegaMenu\Model\Preview|null
     */
    protected $previewModel = null;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $_categoryRepository;

    private $_catOptions;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magenest\MegaMenu\Model\MegaMenuFactory $menuFactory
     * @param \Magenest\MegaMenu\Model\MenuEntityFactory $menuEntityFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magenest\MegaMenu\Model\PreviewFactory $previewFactory
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magenest\MegaMenu\Model\Source\Config\CategoryTree $categoryTree
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magenest\MegaMenu\Model\MegaMenuFactory $menuFactory,
        \Magenest\MegaMenu\Model\MenuEntityFactory $menuEntityFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magenest\MegaMenu\Model\PreviewFactory $previewFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magenest\MegaMenu\Model\Source\Config\CategoryTree $categoryTree,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\View\Asset\Repository $repository
    ) {
        $this->_catOptions = $categoryTree;
        $this->_menuFactory = $menuFactory;
        $this->_menuEntityFactory = $menuEntityFactory;
        $this->_coreRegistry = $registry;
        $this->cacheTypeList = $cacheTypeList;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->storeManager = $storeManager;
        $this->_pageFactory = $pageFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryRepository = $categoryRepository;
        $this->_previewFactory = $previewFactory;
        $this->_formFactory = $formFactory;
        $this->_filterProvider = $filterProvider;
        $this->blockFactory = $blockFactory;
        $this->repository = $repository;
        parent::__construct($context);
    }

    /**
     * Render HTML Content
     *
     * @param $content
     * @return string
     */
    public function renderHTMLContent($content)
    {
        try {
            $html = $this->_filterProvider->getPageFilter()->filter($content);
        } catch (\Exception $e) {
            $html = $e->getMessage();
        }

        return $html;
    }

    /**
     * Get item data
     *
     * @param $itemData
     * @param $items
     */
    public function getItemData(&$itemData, $items)
    {
        $itemData['hasChild'] = 'no';

        if ($itemData['mainContentType'] == null) {
            $itemData['mainContentType'] = 'grid';
            if (!isset($itemData['mainColumn']) || $itemData['mainColumn'] == null) {
                $itemData['mainColumn'] = 4;
            }
        }

        $children = array_unique(explode(',', $itemData['children']));

        if (count($children) > 1) {
            $itemData['hasChild'] = 'yes';

            if ($itemData['mainEnable'] == 1) {
                if ($itemData['mainContentHtml'] == null
                    && $itemData['leftContentHtml'] == null
                    && $itemData['rightContentHtml'] == null
                    && strlen($itemData['children']) > 0
                    && !$this->isChildOfTabMenu($itemData, $items)
                ) {
                    $itemData['mainEnable'] = '0';
                }
            }
        }

        foreach ($children as $child) {
            if (isset($items[$child])) {
                $child = $items[$child];
                $this->getItemData($child, $items);
                $itemData['childrenraw'][] = $child;
            }
        }
    }

    /**
     * @param $itemData
     * @param $items
     * @return bool
     */
    private function isChildOfTabMenu($itemData, $items)
    {
        $id = $itemData['id'] ?? false;
        if (!$id) {
            return false;
        }
        $parent = $this->getParentData($id, $items);

        return ($parent['mainContentType'] ?? false) === 'tabs';
    }

    /**
     * @param $id
     * @param $items
     * @return array
     */
    private function getParentData($id, $items)
    {
        if (!is_array($items)) {
            return [];
        }
        foreach ($items as $item) {
            if (!is_string($item['children'] ?? false) || strlen($item['children'] ?? "") < 1) {
                continue;
            }
            $children = explode(',', $item['children']);
            if (is_array($children) && in_array($id, $children)) {
                return $item;
            }
        }

        return [];
    }

    /**
     * Get active mega menu
     *
     * @param null $menuId
     * @param null $storeId
     * @return $this|array|\Magento\Framework\DataObject
     * @throws \Zend_Json_Exception
     */
    public function getActiveMegaMenu($menuId = null, $storeId = null)
    {
        if ($this->getPreviewModel()) {
            $previewData = \Zend_Json::decode($this->previewModel->getData('data'));

            return [
                'menu' => $this->_menuFactory->create()->addData($previewData),
                'items' => $previewData['menu']
            ];
        }
        $menu = $this->_menuFactory->create()->loadByAlias($menuId, $storeId);

        if ($menu->getId()) {
            return $menu;
        }

        return $this->_menuFactory->create()->getCollection()->getFirstItem();
    }

    /**
     * @return \Magenest\MegaMenu\Model\Preview|null
     */
    public function getPreviewModel()
    {
        return $this->previewModel;
    }

    /**
     * @param \Magenest\MegaMenu\Model\Preview|null $previewModel
     */
    public function setPreviewModel($previewModel)
    {
        $this->previewModel = $previewModel;
    }

    /**
     * Get effect config
     *
     * @param null|int $storeId
     * @return mixed
     */
    public function getEffectConfig($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::EFFECT_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is enable
     *
     * @param null|int $storeId
     * @return mixed
     */
    public function isEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::ENABLE_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isBackupEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::BACKUP_ENABLE_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getNumberOfBackup($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::NUMBER_BACKUP_VERSION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get color
     *
     * @param null|int $storeId
     * @return mixed
     */
    public function getColorConfig($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::HOV_BTN_BG_COLOR_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * render Wysiwyg element
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function renderWysiwygElement()
    {
        return $this->_formFactory->create()->addField(
            '_versatile_editor',
            Editor::class,
            [
                'force_load' => true,
                'rows' => 20,
                'name' => 'Versatile Editor',
                'config' => $this->_wysiwygConfig->getConfig([
                    'add_variables' => true,
                    'add_widgets' => true,
                    'add_directives' => true,
                    'use_container' => true,
                    'container_class' => 'hor-scroll',
                    'skip_widgets' => ['Magenest\MegaMenu\Block\Widget\Menu']
                ]),
                'wysiwyg' => true,
            ]
        )->toHtml();
    }

    /**
     * Get menu Config
     *
     * @param int $menuId
     * @return array|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig($menuId = null)
    {
        $result = [
            'menu_id' => '',
            'menu_name' => '',
            'is_active' => '',
            'pages' => $this->getPages(),
            'menu_item_nest' => [],
            'cats' => $this->getCategories(),
            'save_url' => $this->_getUrl('menu/menu/save'),
            'tooltip_img' => $this->getViewFileUrl('Magenest_MegaMenu::image/tooltip.png'),
            'color_img' => $this->getViewFileUrl('Magenest_MegaMenu::image/color-icons.png'),
            'wysiwyg_config' => $this->_wysiwygConfig->getConfig()->getData()
        ];
        if ($menuId === null) {
            $menuModel = $this->_coreRegistry->registry('magenest_mega_menu');
        } else {
            $menuModel = $this->_menuFactory->create()->load($menuId);
        }

        if (!$menuModel->getId()) {
            return $result;
        }

        return array_merge($result, [
            'menu_id' => $menuModel->getId(),
            'menu_name' => $menuModel->getMenuName(),
            'is_active' => $menuModel->getIsActive(),
            'menu_item_nest' => $this->getMenuNestItems($menuModel),
        ]);
    }

    /**
     * @param MegaMenu $menuModel
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getMenuNestItems($menuModel)
    {
        /** get the menu item associated with the menu */
        $menuItemCollection = $menuModel->getResource()->getEntityCollection($menuModel);
        $items = [];
        $nestMenu = [];
        $oldIds = [];

        if ($menuModel->getIsBackupVersion()) {
            $nestMenu = $menuModel->getMenuBackupData();
            $nestMenu = gzuncompress(base64_decode($nestMenu));
            if (!empty($nestMenu)) {
                $nestMenu = json_decode($nestMenu, true);
            } else {
                $nestMenu = [];
            }
        }

        if ($menuItemCollection->getSize()) {
            /** @var \Magenest\MegaMenu\Model\MenuEntity $menuItem */
            foreach ($menuItemCollection as $menuItem) {
                $menuItem->load($menuItem->getEntityId());
                if ($menuModel->getIsBackupVersion() && array_key_exists($menuItem->getId(), $nestMenu)) {
                    $menuItem = $this->processMenuItem($menuItem, $nestMenu[$menuItem->getId()]);
                    unset($nestMenu[$menuItem->getId()]);
                } elseif ($menuModel->getIsBackupVersion() && !array_key_exists($menuItem->getId(), $nestMenu)) {
                    continue;
                }
                $level = $menuItem->getData('level');
                if ($level || $level === null) {
                    continue;
                }

                $menuItemData = $menuItem->getChildrenTreeFormat();
                $menuItemData['id'] = $menuItem->getEntityId();
                $menuItemData['is_new'] = 'false';
                $oldIds[] = $menuItemData['id'];
                $items[] = $menuItemData;
            }
        }
        if ($menuModel->getIsBackupVersion() && is_array($nestMenu) && !empty($nestMenu)) {
            foreach ($nestMenu as $menu) {
                $menuItem = $this->_menuEntityFactory->create();
                $menuItem = $this->processMenuItem($menuItem, $menu);
                $level = $menuItem->getData('level');
                if ($level || $level === null) {
                    continue;
                }

                $menuItemData = $menuItem->getChildrenTreeFormat();
                $menuItemData['id'] = $this->getNewMaxId($oldIds);
                $menuItemData['is_new'] = 'true';
                $oldIds[] = $menuItemData['id'];
                $items[] = $menuItemData;
            }
        }

        return $items;
    }

    public function getNewMaxId($ids)
    {
        foreach ($ids as $id) {
            $nextValue = (int)$id + 1;
            if (!in_array($nextValue, $ids)) {
                return $nextValue;
            }
        }

        return max($ids) + 1;
    }

    protected function processMenuItem(\Magenest\MegaMenu\Model\MenuEntity $menu, array $switchData)
    {
        $combinedData = [];
        if (array_key_exists('data', $switchData)) {
            $combinedData = json_decode($switchData['data'], true);
            unset($switchData['data']);
        }
        $menuData = array_merge_recursive($combinedData, $switchData);
        unset($menuData['id']);
        $menu->addData($menuData);

        return $menu;
    }

    /**
     * Get CMS pages
     *
     * @return array
     */
    public function getPages()
    {
        $pages = $this->_pageFactory->create()->getCollection();
        $result = [];

        if ($pages->getSize()) {
            foreach ($pages as $page) {
                $pageData['title'] = $page->getTitle();
                $pageData['id'] = $page->getId();
                $pageData['page_id'] = $page->getId();
                $pageData['link'] = $page->getIdentifier();
                $result[] = $pageData;
            }
        }

        return $result;
    }

    /**
     * Get categories
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategories()
    {
        /** @var \Magento\Catalog\Model\Category $categoryModel */
        $categoryModel = $this->_categoryFactory->create();
        $categoryCollection = $categoryModel->getCategories(
            $this->storeManager->getStore()->getRootCategoryId(),
            4,
            false,
            true,
            false
        );
        $categoryCollection->addNameToResult()->load();
        $result = [];

        if ($categoryCollection->getSize()) {
            foreach ($categoryCollection as $category) {
                $result[] = $this->getChildCategory($category);
            }
        }

        return $result;
    }

    private function getChildCategory(Category $category)
    {
        $titlePrefix = "";
        for ($i = 2; $i < $category->getLevel(); $i++) {
            $titlePrefix .= $i === 2 ? "|" : "";
            $titlePrefix .= "_ _ _ _";
        }
        $category->setTitle($titlePrefix . $category->getName());
        $category->setCatId($category->getEntityId());
        $category->setLink($category->getRequestPath());

        return $category->getData();
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @return string
     */
    public function getViewFileUrl($fileId)
    {
        try {
            return $this->repository->getUrlWithParams($fileId, []);
        } catch (LocalizedException $e) {
            $this->_logger->critical($e);

            return '';
        }
    }

    /**
     * Get html menu
     *
     * @param $top
     * @param $effect
     * @param $helper
     * @param array $classes
     * @param null|\Magenest\MegaMenu\Block\Menu\Entity $parent
     * @param null $childHtml
     * @return string
     */
    public function getHtmlMenu($top, $effect, $helper, $classes, $parent = null, $childHtml = null)
    {
        $block = $this->blockFactory->createBlock(
            \Magenest\MegaMenu\Block\Menu\Entity::class,
            [
                'data' => [
                    'top' => $top,
                    'effect' => $effect,
                    'helper' => $helper,
                    'classes' => $classes,
                    'parent_menu' => $parent,
                    'child_menu_html' => $childHtml
                ]
            ]
        );

        return $block->toHtml();
    }

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    public function getMegaOptionConfig()
    {
        return [
            'categoryList' => $this->_catOptions->getOptionMenuItem()
        ];
    }
}
