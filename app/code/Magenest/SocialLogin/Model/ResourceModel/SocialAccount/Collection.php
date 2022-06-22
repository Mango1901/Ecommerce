<?php


namespace Magenest\SocialLogin\Model\ResourceModel\SocialAccount;


/**
 * Class Collection
 * @package Magenest\SocialLogin\Model\ResourceModel\SocialAccount
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     *
     */
    public function _construct()
    {
        $this->_init(\Magenest\SocialLogin\Model\SocialAccount::class,\Magenest\SocialLogin\Model\ResourceModel\SocialAccount::class);
    }
}