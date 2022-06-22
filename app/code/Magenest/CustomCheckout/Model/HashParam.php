<?php

namespace Magenest\CustomCheckout\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class HashParam Model
 */
class HashParam extends AbstractModel implements IdentityInterface
{
    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'pk';

    const CACHE_TAG = 'magenest_hash_param';

    protected $_cacheTag = 'magenest_hash_param';

    protected $_eventPrefix = 'magenest_hash_param';

    /**
     * Function _construct
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\HashParam::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
