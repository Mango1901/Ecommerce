<?php


namespace Magenest\SocialLogin\Block\Adminhtml\SocialAccount\Monitor;


use Magento\Backend\Block\Template;

/**
 * Class SocialAccountGeneric
 * @package Magenest\SocialLogin\Block\Adminhtml\SocialAccount\Monitor
 */
class SocialAccountGeneric extends Template
{
    /**
     * @var
     */
    protected $socialLoginData;
    /**
     * @var \Magenest\SocialLogin\Helper\SocialLogin
     */
    protected $socialLoginHelper;
    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount
     */
    protected $socialAccountResource;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * SocialAccountGeneric constructor.
     * @param \Magenest\SocialLogin\Helper\SocialLogin $socialLoginHelper
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magenest\SocialLogin\Helper\SocialLogin $socialLoginHelper,
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->socialLoginHelper = $socialLoginHelper;
        $this->socialAccountResource = $socialAccountResource;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSocialLoginData() {
        if ($this->socialLoginData){
            return $this->socialLoginData;
        }
        $select = $this->socialAccountResource->getConnection()
                                              ->select()
                                              ->from(['main_table' => $this->socialAccountResource->getMainTable()],
                                                     [
                                                         'social_login_type' => 'social_login_type',
                                                         'users' => 'COUNT(DISTINCT social_login_id)'
                                                     ])
                                              ->joinLeft(
                                                  ['so'=>'sales_order'],
                                                  'main_table.customer_id = so.customer_id and main_table.social_login_type = so.magenest_social_type',
                                                  [
                                                      'purchased_items' => 'SUM(so.total_qty_ordered)',
                                                      'amount' => 'SUM(so.subtotal)'
                                                  ])
                                              ->group('social_login_type')
                                              ->order('users desc')
                                              ->order('amount desc');
        $this->socialLoginData = $this->socialAccountResource->getConnection()->fetchAll($select);
        return $this->socialLoginData;
    }

    /**
     * @param $price
     * @return float|string
     */
    public function getPriceFormat($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}