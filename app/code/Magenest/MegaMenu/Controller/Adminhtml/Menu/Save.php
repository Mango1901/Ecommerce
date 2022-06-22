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

namespace Magenest\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Filesystem;
use Magento\PageCache\Model\Cache\Type;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Cache\Type\Block;
use Magenest\MegaMenu\Model\MegaMenuFactory;
use Magenest\MegaMenu\Model\MenuEntityFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use JsonSchema\Exception\ValidationException;
use Magento\Framework\Validator\Exception;

/**
 * Class Save
 * @package Magenest\MegaMenu\Controller\Adminhtml\Menu
 */
class Save extends Action
{
    /**
     * @var \Magenest\MegaMenu\Model\Menu
     */
    protected $_model;

    /**
     * @var \Magenest\MegaMenu\Model\MegaMenuFactory
     */
    protected $_menuFactory;

    /**
     * @var \Magenest\MegaMenu\Model\MenuEntityFactory
     */
    protected $_menuItemFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param MegaMenuFactory $menuFactory
     * @param MenuEntityFactory $menuEntityFactory
     * @param TypeListInterface $typeList
     * @param Filesystem $filesystem
     * @param ImageUploader $imageUploader
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Action\Context $context,
        MegaMenuFactory $menuFactory,
        MenuEntityFactory $menuEntityFactory,
        TypeListInterface $typeList,
        Filesystem $filesystem,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->_menuFactory = $menuFactory;
        $this->_menuItemFactory = $menuEntityFactory;
        $this->typeList = $typeList;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageUploader = $imageUploader;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $action = $this->getRequest()->getParam('action');
        try {
            $params = $this->getRequest()->getParams();
            $deleteItemsStr = $this->getRequest()->getParam('remove_items');
            $deleteItems = [];

            if ($deleteItemsStr) {
                $deleteItems = $this->deleteNestedMenu($deleteItemsStr);
            }

            /** Save a menu with general information like id, title, level, order, parentId ... */
            $megaMenuData = $this->_processMegaMenuParams($params);
            $menuId = $megaMenuData['menu_id'];
            $megaMenu = $this->saveMenu($megaMenuData);

            unset($params['menu_id']);
            unset($params['menu_name']);
            unset($params['is_active']);
            unset($params['store_id']);
            $menuId = $megaMenu->getId();
            if (!isset($params['menu'])) {
                $this->deleteNestedMenu(false, $menuId);
                $this->messageManager->addSuccessMessage(__("Menu data has been successfully saved."));
                if ($action === 'save_and_continue') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $menuId]);
                }

                return $resultRedirect->setPath('*/*/');
            }
            $childResult = $this->processMenuItem($params['menu'], $menuId, $deleteItems);
            list($mappedIds, $savedMenuItems) = $childResult;
            $this->updateChildren($mappedIds, $savedMenuItems);
            //Remove this code: Due to slow performance
//            $this->typeList->invalidate([
//                Block::TYPE_IDENTIFIER,
//                Type::TYPE_IDENTIFIER
//            ]);
//            $this->typeList->cleanType('full_page');
//            $this->typeList->cleanType('block_html');

            $this->messageManager->addSuccessMessage(__("Menu data has been successfully saved."));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        if ($action == 'save_and_continue') {
            return $resultRedirect->setPath('*/*/edit', ['id' => $menuId]);
        }

        return $resultRedirect->setPath('menu/menu/');
    }

    /**
     * Save mega menu
     *
     * @param array $postData
     * @return mixed
     * @throws \Exception
     */
    private function saveMenu($postData)
    {
        $menuId = (int)$postData['menu_id'];

        if ($menuId) {
            $menuModel = $this->_menuFactory->create()->load($menuId);
            $versionMenu = (int)$menuModel->getCurrentVersion();
            $menuModel->setCurrentVersion($versionMenu + 1);
            $menuModel->addData($postData)->save();
        } else {
            unset($postData['menu_id']);
            $menuModel = $this->_menuFactory->create();
            $menuModel->setCurrentVersion(1);
            $menuModel->setData($postData)->save();
        }

        return $menuModel;
    }

    private function saveMenuItem($menuData)
    {
        $valid = $this->validateMenuDateItem($menuData);

        if ($valid['result']) {
            $menuItem = $this->_menuItemFactory->create();
            $menuItem->setData($menuData);
            $menuItem->save();
            return $menuItem;
        }

        return null;
    }

    /**
     * validate the input
     * @param $menuData
     * @return array
     */
    private function validateMenuDateItem($menuData)
    {
        $valid = new \Zend_Validate_NotEmpty();

        return [
            'result' => $valid->isValid($menuData['title']),
            'message' => __('Title of menu item cant be empty')
        ];
    }

    /**
     * 1 2,3,4
     * 10 11,23,44
     *  ['1'=> 10, '2=> 11, 23 => 13]
     * mappedId;
     * @param $mappedIds
     * @param $menus
     */
    public function updateChildren($mappedIds, $menus)
    {
        foreach ($menus as $menuItem) {
            $real = [];
            $children = explode(',', $menuItem->getData('children_temp'));
            if ($children) {
                foreach ($children as $id) {
                    if (isset($mappedIds[$id])) {
                        $real[] = $mappedIds[$id];
                    }
                }
            }

            $realChildStr = implode(',', $real);
            $menuItem->addData(['children' => $realChildStr])->save();
        }
    }

    private function deleteNestedMenu($deleteItemsStr = false, $menuId = null)
    {
        if ($deleteItemsStr) {
            $deleteItems = explode(',', $deleteItemsStr);
            if (is_array($deleteItems) && !empty($deleteItems)) {
                $this->_menuItemFactory->create()->getCollection()->addFieldToFilter('entity_id', ['in' => $deleteItems])->walk('delete');
            }

            return $deleteItems;
        }

        if (!$deleteItemsStr && $menuId) {
            $this->_menuItemFactory->create()->getCollection()->addFieldToFilter('menu_id', $menuId)->walk('delete');
        }

        return true;
    }

    private function _processMegaMenuParams($params)
    {
        $megaMenuData = [
            'menu_id' => 0,
            'menu_name' => '',
            'menu_template' => 'horizontal',
            'menu_top' => 'auto',
            'store_id' => 0
        ];

        $megaMenuData['menu_id'] = (isset($params['menu_id'])) ? $params['menu_id'] : null;
        $megaMenuData['menu_name'] = $params['menu_name'];
        $megaMenuData['menu_template'] = $params['menu_template'];
        $megaMenuData['menu_top'] = $params['menu_top'] ?? "";
        $megaMenuData['custom_css'] = $params['custom_css'];
        $megaMenuData['menu_alias'] = $params['menu_alias'];
        $megaMenuData['event'] = $params['event'];
        $megaMenuData['scroll_to_fixed'] = $params['scroll_to_fixed'];
        $megaMenuData['customer_group_ids'] = isset($params['customer_group_ids']) && !empty($params['customer_group_ids']) ?
            implode(',', $params['customer_group_ids']) : "";
        $megaMenuData['store_id'] = isset($params['stores']) && !empty($params['stores']) ?
            implode(',', $params['stores']) : "";
        $megaMenuData['mobile_template'] = $params['mobile_template'];
        $megaMenuData['disable_iblocks'] = $params['disable_iblocks'];
        if (array_key_exists('menu', $params)) {
            $megaMenuData['child_menus'] = json_encode($params['menu']);
        }

        return $megaMenuData;
    }

    /**
     * @param $menuParams
     * @param $menuId
     * @param $deleteItems
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Json_Exception
     */
    protected function processMenuItem($menuParams, $menuId, $deleteItems)
    {
        if (is_array($menuParams)) {
            $mappedIds = $savedMenuItems = $updatedMenu = [];
            foreach ($menuParams as $itemId => $rawData) {
                if (in_array($itemId, $deleteItems)) {
                    continue;
                }
                if (isset($rawData['data']) && !empty($rawData['data'])) {
                    $rawData = array_merge($rawData, \Zend_Json::decode($rawData['data']));
                }
                //calibrate its children
                if (isset($rawData['children'])) {
                    if ($rawData['children'] != '0') {
                        $rawData['children'] = substr($rawData['children'], 2);
                    } else {
                        $rawData['children'] = '';
                    }
                }

                $menuData = [
                    'id' => 0,
                    'entity_id' => 0,
                    'menu_id' => 0,
                    'title' => '',
                    'level' => 0,
                    'sort' => 0,
                    'parentId' => 0,
                    'type' => '',
                    'is_mega' => '',
                    'is_top' => '',
                    'parent_id' => '',
                    'link' => '',
                    'label' => 0,
                    'obj_id' => '',
                    'brother_name' => '',
                    'icon' => '',
                    'id_temp' => '',
                    'label_color' => '',
                    'label_name' => '',
                    'megaColumn' => 6,
                    'include_child' => '',
                    'show_product' => '',
                    'children' => '',
                    'children_temp' => '',
                    'cssClass' => '',
                    'showIcon' => '',
                    'leftEnable' => '',
                    'leftClass' => '',
                    'leftWidth' => '',
                    'leftContentHtml' => '',
                    'mainEnable' => 0,
                    'mainProductSku' => '',
                    'mainInCategory' => '',
                    'mainContentType' => '',
                    'mainColumn' => '',
                    'mainContentHtml' => '',
                    'rightEnable' => '',
                    'rightClass' => '',
                    'rightWidth' => '',
                    'rightContentHtml' => '',
                    'headerEnable' => '',
                    'headerClass' => '',
                    'headerContentHtml' => '',
                    'footerEnable' => '',
                    'footerClass' => '',
                    'footerContentHtml' => '',
                    'color' => '',
                    'textColor' => '',
                    'hoverTextColor' => '',
                    'hoverIconColor' => '',
                    'background_image' => '',
                    'backgroundColor' => '',
                    'backgroundWidth' => '',
                    'backgroundHeight' => '',
                    'backgroundOpacity' => '',
                    'itemEnable' => '',
                    'linkTarget' => '',
                    'cssInline' => '',
                    'hideText' => '',
                    'background_position_x' => '',
                    'background_position_y' => '',
                    'animation_time' => '',
                    'animation_in' => '',
                    'main_content_width' => '',
                    'parent_category_content' => '',
                    'backgroundRepeat' => '',
                    'backgroundSize' => '',
                    'animateDelayTime' => '',
                    'animateSpeed' => '',
                    'menu_type' => ''
                ];
                $editMode = !isset($rawData['is_new']) || $rawData['is_new'] != 'true';

                foreach ($menuData as $key => $value) {
                    if (isset($rawData[$key])) {
                        $menuData[$key] = $rawData[$key];
                    }
                }

                $menuData['entity_id'] = $menuData['id'];
                $menuData['children_temp'] = $menuData['children'];
                $menuData['menu_id'] = $menuId;

                if (isset($rawData['id'])) {
                    $menuData['id_temp'] = $rawData['id'];
                }

                if (!$editMode) {
                    unset($menuData['id']);
                    unset($menuData['entity_id']);
                }

                $menu = $this->saveMenuItem($menuData);
                $updatedMenu[] = $menu->getId();

                if (isset($menuData['icon']) && $menuData['icon'] != '') {
                    $baseTmpImagePath = $this->imageUploader->getFilePath($this->imageUploader->getBaseTmpPath(), $menuData['icon']);

                    if ($this->mediaDirectory->isExist($baseTmpImagePath)) {
                        $this->imageUploader->moveFileFromTmp($menuData['icon']);
                    }
                }

                if (isset($menuData['background_image']) && $menuData['background_image'] != '') {
                    $baseTmpImagePath = $this->imageUploader->getFilePath($this->imageUploader->getBaseTmpPath(), $menuData['background_image']);

                    if ($this->mediaDirectory->isExist($baseTmpImagePath)) {
                        $this->imageUploader->moveFileFromTmp($menuData['background_image']);
                    }
                }
                if ($menu == null) {
                    continue;
                }

                $mappedIds[$rawData['id']] = $menu->getId();
                $savedMenuItems[] = $menu;
            }
            $this->deleteUnUpdatedMenu($updatedMenu, $menuId);

            return [$mappedIds, $savedMenuItems];
        }

        return [];
    }

    private function deleteUnUpdatedMenu($ids, $menuId)
    {
        $this->_menuItemFactory->create()->getCollection()
            ->addFieldToFilter('menu_id', $menuId)
            ->addFieldToFilter('entity_id', ['nin' => $ids])
            ->walk('delete');
    }
}
