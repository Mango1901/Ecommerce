<?php
namespace Magenest\CustomCheckout\Model\ResourceModel\HashParam;

use Magenest\CustomCheckout\Model\ResourceModel\HashParam as HashParamResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magenest\CustomCheckout\Model\HashParam as HashParamModel;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'pk';

    protected $_eventPrefix = 'magenest_hash_param_collection';

    protected $_eventObject = 'hash_param_collection';

    /**
     * Define collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(HashParamModel::class, HashParamResourceModel::class);
    }
}
