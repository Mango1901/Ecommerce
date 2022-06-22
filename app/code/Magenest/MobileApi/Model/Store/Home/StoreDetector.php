<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Store\Home;

use Magenest\MobileApi\Api\Data\Store\HomeExtensionInterfaceFactory;
use Magenest\MobileApi\Api\Data\Store\HomeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\MobileApi\Model\Store\Home\Renderer\DefaultInterface;

/**
 * Class StoreDetector
 * @package Magenest\MobileApi\Model\Store\Home
 */
class StoreDetector
{
    /**
     * @var HomeExtensionInterfaceFactory
     */
    protected $_homeExtensionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var array
     */
    protected $_storeComposite = [];

    /**
     * Constructor.
     *
     * @param HomeExtensionInterfaceFactory $homeExtensionInterfaceFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param array $storeComposite
     */
    function __construct(
        HomeExtensionInterfaceFactory $homeExtensionInterfaceFactory,
        StoreManagerInterface $storeManagerInterface,
        $storeComposite = []
    )
    {
        $this->_storeComposite = $storeComposite;
        $this->_homeExtensionFactory = $homeExtensionInterfaceFactory;
        $this->_storeManager = $storeManagerInterface;
    }

    /**
     * Detect home data per store
     *
     * @param HomeInterface $home
     */
    public function detect(HomeInterface $home)
    {
        $extensionAttributes = $home->getExtensionAttributes();
        $store = $this->_storeManager->getStore()->getCode();

        if (!$extensionAttributes) {
            $extensionAttributes = $this->_homeExtensionFactory->create();
        }

        /** Render store data */
        if (isset($this->_storeComposite[$store])) {
            /** @var \Magenest\MobileApi\Model\Store\Home\Renderer\DefaultInterface $storeRenderer */
            foreach ($this->_storeComposite[$store] as $storeRenderer) {
                $storeRenderer->process($extensionAttributes);
            }
        }

        $home->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get store renderer
     *
     * @param string $name
     * @return DefaultInterface|null
     */
    public function getStoreRenderer($name)
    {
        $store = $this->_storeManager->getStore()->getCode();
        if (isset($this->_storeComposite[$store][$name])) {
            return $this->_storeComposite[$store][$name];
        }

        return null;
    }
}