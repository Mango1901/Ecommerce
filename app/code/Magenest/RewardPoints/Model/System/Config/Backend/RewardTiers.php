<?php

namespace Magenest\RewardPoints\Model\System\Config\Backend;

use Magento\Framework\App\ObjectManager;

/**
 * Class RewardTiers
 * @package Magenest\RewardPoints\Model\System\Config\Backend
 */
class RewardTiers extends \Magento\Framework\App\Config\Value
{

    /**
     * @var \Magento\CatalogInventory\Helper\Minsaleqty|null
     */
    protected $_catalogInventoryMinsaleqty = null;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $_serialize;

    /**
     * RewardTiers constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\CatalogInventory\Helper\Minsaleqty $catalogInventoryMinsaleqty
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\CatalogInventory\Helper\Minsaleqty $catalogInventoryMinsaleqty,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        array $data = []
    ) {
        $this->_catalogInventoryMinsaleqty = $catalogInventoryMinsaleqty;
        $this->_serialize = $serialize;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return \Magento\Framework\App\Config\Value|void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (count($value) > 1) {
            unset($value['__empty']);
            if (isset($value)) {
                $this->setValue($this->_serialize->serialize($value));
            } else {
                return null;
            }
        } else {
            $scopeConfig = ObjectManager::getInstance()->create('\Magento\Framework\App\Config\ScopeConfigInterface');
            $value       = $scopeConfig->getValue('rewardpoints/reward_tiers/discount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->setValue($value);
        }
    }

    /**
     * @return \Magento\Framework\App\Config\Value|void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (isset($value)) {
            $this->setValue($this->_serialize->unserialize($value));
        } else {
            return null;
        }
    }

}
