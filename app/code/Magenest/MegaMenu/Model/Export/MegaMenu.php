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

namespace Magenest\MegaMenu\Model\Export;

use Magenest\MegaMenu\Model\MenuEntityFactory;
use Magenest\MegaMenu\Model\ResourceModel\MegaMenu\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\ImportExport\Model\Export\Factory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\Store\Model\StoreManagerInterface;

class MegaMenu extends AbstractEntity
{
    const MAGENEST_MEGAMENU = 'magenest_mega_menu';

    const MENU_ID = 'menu_id';
    const MENU_NAME = 'menu_name';
    const STORE_ID = 'store_id';
    const MENU_TEMPLATE = 'menu_template';
    const ID_TEMP = 'id_temp';
    const CUSTOM_CSS = 'custom_css';
    const MENU_TOP = 'menu_top';
    const MENU_ALIAS = 'menu_alias';
    const EVENT = 'event';
    const SCROLL_TO_FIXED = 'scroll_to_fixed';
    const MOBILE_TEMPLATE = 'mobile_template';
	const DISABLE_IBLOCKS = 'disable_iblocks';

    protected $megaMenuCollection;

    protected $menuEntity;

    private $_entityCollection;

    protected $_attributeCollectionProvider;

	protected $_request;

	protected $_menuEntityCollection;

	/**
	 * MegaMenu constructor.
	 * @param RequestInterface $request
	 * @param ScopeConfigInterface $scopeConfig
	 * @param StoreManagerInterface $storeManager
	 * @param Factory $collectionFactory
	 * @param AttributeCollectionProvider $attributeCollectionProvider
	 * @param CollectionByPagesIteratorFactory $resourceColFactory
	 * @param CollectionFactory $megaMenuCollection
	 * @param MenuEntityFactory $menuEntity
	 * @param \Magenest\MegaMenu\Model\ResourceModel\MenuEntity\CollectionFactory $menuEntityCollection
	 * @param array $data
	 */
    public function __construct(
		RequestInterface $request,
		ScopeConfigInterface $scopeConfig,
		StoreManagerInterface $storeManager,
		Factory $collectionFactory,
		AttributeCollectionProvider $attributeCollectionProvider,
		CollectionByPagesIteratorFactory $resourceColFactory,
		CollectionFactory $megaMenuCollection,
		MenuEntityFactory $menuEntity,
		\Magenest\MegaMenu\Model\ResourceModel\MenuEntity\CollectionFactory $menuEntityCollection,
		array $data = []
    ) {
		$this->_request = $request;
		$this->_attributeCollectionProvider = $attributeCollectionProvider;
        $this->megaMenuCollection = $megaMenuCollection;
        $this->menuEntity = $menuEntity;
        $this->_menuEntityCollection = $menuEntityCollection;
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
    }

    public function getItemData(){
		return $this->_request->getParam('export_filter');
	}

    public function getMenuIdFrom(){
    	return $this->getItemData()['menu_id'][0];
	}
	public function getMenuIdTo(){
		return $this->getItemData()['menu_id'][1];
	}
	public function getMenuName(){
		return $this->getItemData()['menu_name'];
	}
	public function getStoreIdFrom(){
		return $this->getItemData()['store_id'][0];
	}
	public function getStoreIdTo(){
		return $this->getItemData()['store_id'][1];
	}
	public function getMenuTemplate(){
		return $this->getItemData()['menu_template'];
	}
	public function getIdTempFrom(){
		return $this->getItemData()['id_temp'][0];
	}
	public function getIdTempTo(){
		return $this->getItemData()['id_temp'][1];
	}
	public function getCustomCss(){
		return $this->getItemData()['custom_css'];
	}
	public function getMenuTop(){
		return $this->getItemData()['menu_top'];
	}
	public function getMenuAlias(){
		return $this->getItemData()['menu_alias'];
	}
	public function getEvent(){
		return $this->getItemData()['event'];
	}
	public function getScrollToFixed(){
		return $this->getItemData()['scroll_to_fixed'];
	}
	public function getMobileTemplate(){
		return $this->getItemData()['mobile_template'];
	}
	public function getDisableIblocks(){
		return $this->getItemData()['disable_iblocks'];
	}
    /**
     * Export process
     *
     * @return string
     */
    public function export()
    {
		$writer = $this->getWriter();
		$megaMenuCollection = $this->megaMenuCollection->create();
		if ($this->getMenuIdFrom()){
			$megaMenuCollection->addFieldToFilter(self::MENU_ID, ['gteq' => $this->getMenuIdFrom()]);
		}
		if ($this->getMenuIdTo()){
			$megaMenuCollection->addFieldToFilter(self::MENU_ID, ['lteq' => $this->getMenuIdTo()]);
		}
		if ($this->getMenuName()){
			$megaMenuCollection->addFieldToFilter(self::MENU_NAME, $this->getMenuName());
		}
		if ($this->getStoreIdFrom()){
			$megaMenuCollection->addFieldToFilter(self::STORE_ID, ['gteq' => $this->getStoreIdFrom()]);
		}
		if ($this->getStoreIdTo()){
			$megaMenuCollection->addFieldToFilter(self::STORE_ID, ['lteq' => $this->getStoreIdTo()]);
		}
		if ($this->getMenuTemplate()){
			$megaMenuCollection->addFieldToFilter(self::MENU_TEMPLATE, $this->getMenuTemplate());
		}
		if ($this->getIdTempFrom()){
			$megaMenuCollection->addFieldToFilter(self::ID_TEMP, ['gteq' => $this->getIdTempFrom()]);
		}
		if ($this->getIdTempTo()){
			$megaMenuCollection->addFieldToFilter(self::ID_TEMP, ['lteq' => $this->getIdTempTo()]);
		}
		if ($this->getCustomCss()){
			$megaMenuCollection->addFieldToFilter(self::CUSTOM_CSS, $this->getCustomCss());
		}
		if ($this->getMenuTop()){
			$megaMenuCollection->addFieldToFilter(self::MENU_TOP, $this->getMenuTop());
		}
		if ($this->getMenuAlias()){
			$megaMenuCollection->addFieldToFilter(self::MENU_ALIAS, $this->getMenuAlias());
		}
		if ($this->getEvent()){
			$megaMenuCollection->addFieldToFilter(self::EVENT, $this->getEvent());
		}
		if ($this->getScrollToFixed()){
			$megaMenuCollection->addFieldToFilter(self::SCROLL_TO_FIXED, $this->getScrollToFixed());
		}
		if ($this->getMobileTemplate()){
			$megaMenuCollection->addFieldToFilter(self::MOBILE_TEMPLATE, $this->getMobileTemplate());
		}
		if ($this->getDisableIblocks()){
			$megaMenuCollection->addFieldToFilter(self::DISABLE_IBLOCKS, $this->getDisableIblocks());
		}
		$writer->setHeaderCols($this->_getHeaderColumns());
		foreach ($megaMenuCollection->getData() as $data) {
			$writer->writeRow($data);
			$menuEntity = $this->_menuEntityCollection->create();
			foreach ($menuEntity as $dataEntity){
				if ($dataEntity->getMenuId() == $data['menu_id']){
					$menuData = $this->menuEntity->create()->setId($dataEntity->getEntityId())->getChildrenTreeFormat();
					$exportData = $this->prepareExportData($menuData);
					$writer->writeRow($exportData);
				}
			}
		}
		// create export file
		return $writer->getContents();
    }

    private function prepareExportData($menuData)
	{
		foreach ($menuData as $key => $value){
			if ($key == 'icon' || $key == 'backgroundImage'){
				$menuData['icon'] = $value['name'];
			}
			if ($key == 'id'){
				$menuData['child_menu_id'] = $menuData['id'];
			}
		}
		return $menuData;
	}

    /**
     * Export one item
     *
     * @param AbstractModel $item
     * @return void
     */
    public function exportItem($item)
    {

    }

    /**
     * Entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::MAGENEST_MEGAMENU;
    }

    /**
     * Get header columns
     *
     * @return array
     */
    protected function _getHeaderColumns()
    {
        return [
            'menu_id',
            'menu_name',
            'store_id',
            'menu_template',
            'id_temp',
            'custom_css',
            'menu_top',
            'menu_alias',
            'event',
            'scroll_to_fixed',
            'mobile_template',
            'disable_iblocks',
			'child_menu_id',
			'title',
			'level',
			'sort',
			'label',
			'mainContentType',
			'mainColumn',
			'mainContentHtml',
			'mainContentWidth',
			'cssClass',
			'leftClass',
			'leftWidth',
			'leftContentHtml',
			'rightClass',
			'rightWidth',
			'rightContentHtml',
			'textColor',
			'hoverTextColor',
			'hoverIconColor',
			'mainEnable',
			'leftEnable',
			'rightEnable',
			'link',
			'cssInline',
			'icon',
			'footerEnable',
			'footerContentHtml',
			'footerClass',
			'headerEnable',
			'headerClass',
			'headerContentHtml',
			'color',
			'backgroundColor',
			'backgroundSize',
			'backgroundOpacity',
			'backgroundPositionX',
			'backgroundPositionY',
			'backgroundImage',
			'backgroundRepeat',
			'animationDelayTime',
			'animationSpeed',
			'animationIn',
			'mainParentCategory',
			'itemEnable',
			'linkTarget',
			'hideText',
			'hasChild'
        ];
    }

    /**
     * Get entity collection
     *
     * @param bool $resetCollection
     * @return AbstractDb
     */
    protected function _getEntityCollection($resetCollection = true)
    {
        if ($resetCollection || empty($this->_entityCollection)) {
            $this->_entityCollection = $this->megaMenuCollection->create();
        }
        return $this->_entityCollection;
    }

    public function getAttributeCollection()
    {
        return $this->_attributeCollectionProvider->get();
    }
}
