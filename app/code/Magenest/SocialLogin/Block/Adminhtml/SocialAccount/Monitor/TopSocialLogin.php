<?php


namespace Magenest\SocialLogin\Block\Adminhtml\SocialAccount\Monitor;


use Magento\Backend\Block\Template;

/**
 * Class TopSocialLogin
 * @package Magenest\SocialLogin\Block\Adminhtml\SocialAccount\Monitor
 */
class TopSocialLogin extends SocialAccountGeneric
{
    /**
     * @return int|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTotalUsers(){
        $totalUsers = 0;
        foreach ($this->getSocialLoginData() as $item) {
            if (isset($item['users'])) {
                $totalUsers += $item['users'];
            }
        }
        return $totalUsers;
    }
}