<?php


namespace Magenest\SocialLogin\Model\ResourceModel;


/**
 * Class SocialAccount
 * @package Magenest\SocialLogin\Model\ResourceModel
 */
class SocialAccount extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magenest_social_login_account','id');
    }
}