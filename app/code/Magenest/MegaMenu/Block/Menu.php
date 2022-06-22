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

namespace Magenest\MegaMenu\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magenest\MegaMenu\Model\MegaMenu;

class Menu extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Element\BlockInterface, IdentityInterface
{
    const MEGAMENU_HTML_CACHE_ID = 'MEGAMENU_HTML_CACHE_ID';

    /**
     * Cache identities
     *
     * @var array
     */
    protected $identities = [];

    /**
     * @var \Magenest\MegaMenu\Model\MegaMenuFactory
     */
    protected $_menuFactory;

    /**
     * Entity constructor.
     *
     * @param \Magento\MediaStorage\Model\File\Storage\File $fileStorage
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\MegaMenu\Helper\DefaultConfig $defaultConfig
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
	public function __construct(
        \Magenest\MegaMenu\Model\MegaMenuFactory $menuFactory,
		\Magento\Framework\View\Element\Template\Context $context,
		array $data = []
	) {
		parent::__construct($context, $data);
        $this->_menuFactory = $menuFactory;
        $this->addData([
            'cache_lifetime' => 86400,
            //'cache_key' => $this->getMegaMenuAlias(),
        ]);
	}

    /**
     * Get active mega menu
     *
     * @param null $menuId
     * @param null $storeId
     * @return $this|array|\Magento\Framework\DataObject
     * @throws \Zend_Json_Exception
     */
    public function getActiveMegaMenu($menuId, $storeId = null)
    {
        if (!$storeId){
            $storeId = $this->_storeManager->getStore()->getId();
        }
        $menu = $this->_menuFactory->create()->loadByAlias($menuId, $storeId);
        if (!$menu->getId()) {
            $menu = $this->_menuFactory->create()->loadByAlias($menuId);
        }
        $this->addIdentity(MegaMenu::CACHE_TAG . '_' . $menu->getId());
        return $menu;
    }

    /**
     * Add identity
     *
     * @param string|array $identity
     * @return void
     */
    public function addIdentity($identity)
    {
        if (!in_array($identity, $this->identities)) {
            $this->identities[] = $identity;
        }
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    public function getCacheKey()
    {
        $key = $this->getCacheKeyInfo();
        $key[] = $this->getMegaMenuAlias();

        $key = array_values($key);  // ignore array keys

        $key = implode('|', $key);
        $key = sha1($key); // use hashing to hide potentially private data
        return static::CACHE_KEY_PREFIX . $key;
    }

    public function toHtml()
    {
        return parent::toHtml();
        //Add backend cache
//        if($html = $this->_cache->load($this->getCacheId())){
//            return $html;
//        }else{
//            $html = parent::toHtml();
//            $this->_cache->save($html, $this->getCacheId(), $this->getIdentities());
//        }
//        return $html;
    }

    private function getCacheId(){
        return self::MEGAMENU_HTML_CACHE_ID."_".strtoupper($this->getMegaMenuAlias());
    }
}
