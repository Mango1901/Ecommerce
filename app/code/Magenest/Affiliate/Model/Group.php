<?php


namespace Magenest\Affiliate\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Group
 * @package Magenest\Affiliate\Model
 */
class Group extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'affiliate_group';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'affiliate_group';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'affiliate_group';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Affiliate\Model\ResourceModel\Group');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
