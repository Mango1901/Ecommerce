<?php


namespace Magenest\SocialLogin\Model;


/**
 * Class SocialAccount
 * @package Magenest\SocialLogin\Model
 */
class SocialAccount extends \Magento\Framework\Model\AbstractModel
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init(\Magenest\SocialLogin\Model\ResourceModel\SocialAccount::class);
    }
}