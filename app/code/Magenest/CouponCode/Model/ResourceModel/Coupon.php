<?php

namespace Magenest\CouponCode\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;

class Coupon extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    public $_associatedEntitiesMap = [];

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\DataObject|null $associatedEntityMapInstance
     * @param mixed $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\DataObject $associatedEntityMapInstance = null,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $associatedEntitiesMapInstance = $associatedEntityMapInstance ?: ObjectManager::getInstance()->get(
            \Magento\SalesRule\Model\ResourceModel\Rule\AssociatedEntityMap::class
        );
        $this->_associatedEntitiesMap = $associatedEntitiesMapInstance->getData();
    }

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init($this->getTable('salesrule_coupon'), 'coupon_id');
    }

    /**
     * Get entities
     *
     * @return array|mixed|null
     */
    public function getAssociatedEntitiesMap()
    {
        return $this->_associatedEntitiesMap;
    }
}
