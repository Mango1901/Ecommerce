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

namespace Magenest\MegaMenu\Model;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class MenuEntity
 * @package Magenest\MegaMenu\Model
 */
class MenuEntity extends AbstractModel
{
    const ENTITY = 'magenest_menu';

    /** @var  array */
    protected $treeInfo;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init('Magenest\MegaMenu\Model\ResourceModel\MenuEntity');
    }

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getChildrenTreeFormat()
    {
        /** @var StoreManagerInterface $storeManager */
        $storeManager = $this->storeManager;
        $this->load($this->getId());
        $this->treeInfo['id'] = $this->getId();
        $this->treeInfo['menu_id'] = $this->getMenuId();
        $this->treeInfo['title'] = $this->getTitle();
        $this->treeInfo['level'] = $this->getData('level');
        $this->treeInfo['sort'] = $this->getData('sort');
        $this->treeInfo['label'] = $this->getData('label');
        $this->treeInfo['mainContentType'] = $this->getData('mainContentType');
        $this->treeInfo['mainColumn'] = $this->getData('mainColumn');
        $this->treeInfo['mainContentHtml'] = $this->getData('mainContentHtml');
        $this->treeInfo['mainContentWidth'] = $this->getData('main_content_width');
        $this->treeInfo['cssClass'] = $this->getData('cssClass');
        $this->treeInfo['leftClass'] = $this->getData('leftClass');
        $this->treeInfo['leftWidth'] = $this->getData('leftWidth');
        $this->treeInfo['leftContentHtml'] = $this->getData('leftContentHtml');
        $this->treeInfo['rightClass'] = $this->getData('rightClass');
        $this->treeInfo['rightWidth'] = $this->getData('rightWidth');
        $this->treeInfo['rightContentHtml'] = $this->getData('rightContentHtml');
        $this->treeInfo['textColor'] = $this->getData('textColor');
        $this->treeInfo['hoverTextColor'] = $this->getData('hoverTextColor');
        $this->treeInfo['hoverIconColor'] = $this->getData('hoverIconColor');
        $this->treeInfo['mainEnable'] = $this->getData('mainEnable');
        $this->treeInfo['leftEnable'] = $this->getData('leftEnable');
        $this->treeInfo['rightEnable'] = $this->getData('rightEnable');
        $this->treeInfo['link'] = $this->getLink();
        $this->treeInfo['cssInline'] = $this->getData('cssInline');
        $this->treeInfo['menu_type'] = $this->getData('menu_type');

        if ($this->getIcon() !== null) {
            $this->treeInfo['icon'] = [
                'name' => $this->getIcon(),
                'previewType' => 'image',
                'url' => $storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . 'mega_menu/item/' . $this->getIcon()
            ];
        }

        if ($this->getBackgroundImage() !== null) {
            $this->treeInfo['backgroundImage'] = [
                'name' => $this->getBackgroundImage(),
                'previewType' => 'image',
                'url' => $storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . 'mega_menu/item/' . $this->getBackgroundImage()
            ];
        }

        $this->treeInfo['footerEnable'] = $this->getData('footerEnable');
        $this->treeInfo['footerContentHtml'] = $this->getData('footerContentHtml');
        $this->treeInfo['footerClass'] = $this->getData('footerClass');
        $this->treeInfo['headerEnable'] = $this->getData('headerEnable');
        $this->treeInfo['headerClass'] = $this->getData('headerClass');
        $this->treeInfo['headerContentHtml'] = $this->getData('headerContentHtml');
        $this->treeInfo['color'] = $this->getData('color');
        $this->treeInfo['backgroundColor'] = $this->getData('backgroundColor');
        $this->treeInfo['backgroundRepeat'] = $this->getData('backgroundRepeat');
        $this->treeInfo['backgroundSize'] = $this->getData('backgroundSize');
        $this->treeInfo['backgroundOpacity'] = $this->getData('backgroundOpacity');
        $this->treeInfo['backgroundPositionX'] = $this->getData('background_position_x');
        $this->treeInfo['backgroundPositionY'] = $this->getData('background_position_y');
        $this->treeInfo['animationDelayTime'] = $this->getData('animateDelayTime');
        $this->treeInfo['animationSpeed'] = $this->getData('animateSpeed');
        $this->treeInfo['animationIn'] = $this->getData('animation_in');
        $this->treeInfo['mainParentCategory'] = $this->getData('parent_category_content');
        $this->treeInfo['itemEnable'] = $this->getData('itemEnable');
        $this->treeInfo['linkTarget'] = $this->getData('linkTarget');
        $this->treeInfo['hideText'] = $this->getData('hideText');
        $this->treeInfo['obj_id'] = $this->getData('obj_id');

        $hasChildrenInfo = $this->getData('children');

        if ($hasChildrenInfo == '' || $hasChildrenInfo == '0') {
            $this->treeInfo['hasChild'] = 'no';
        } else {
            $this->treeInfo['hasChild'] = 'yes';
        }

        $menus = $this->getF1Children();

        if ($menus->getSize() > 0) {
            foreach ($menus as $theMenu) {
                /** @var MenuEntity $theMenu */
                $childrenInfo = $theMenu->getChildrenTreeFormat();
                $this->treeInfo['childrenraw'][] = $childrenInfo;
            }
        }

        return $this->treeInfo;
    }

    /**
     *  Get F1 children
     */
    public function getF1Children()
    {
        $children = explode(',', $this->getData('children'));
        if (!empty($children)) {
            return $this->getCollection()->addFieldToFilter('entity_id', ['in' => $children])
                ->setOrder('sort', Select::SQL_ASC)->load();
        }

        return null;
    }
}
