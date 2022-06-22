<?php


namespace Magenest\SocialLogin\Block\Adminhtml\SocialAccount\Monitor;


use Magento\Backend\Block\Template;

/**
 * Class SocialLoginChart
 * @package Magenest\SocialLogin\Block\Adminhtml\SocialAccount\Monitor
 */
class SocialLoginChart extends Template
{
    /**
     * @var null
     */
    private $chartData = null;
    /**
     * @var \Magenest\SocialLogin\Helper\SocialLogin
     */
    protected $socialLoginHelper;
    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount
     */
    protected $socialAccountResource;

    /**
     * SocialLoginChart constructor.
     * @param \Magenest\SocialLogin\Helper\SocialLogin $socialLoginHelper
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magenest\SocialLogin\Helper\SocialLogin $socialLoginHelper,
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->socialLoginHelper = $socialLoginHelper;
        $this->socialAccountResource = $socialAccountResource;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChartData() {
        if ($this->chartData){
            return $this->chartData;
        }
        $select = $this->socialAccountResource->getConnection()
                                              ->select()
                                              ->from($this->socialAccountResource->getMainTable(),
                                                  [
                                                      'social_login_type' => 'social_login_type',
                                                      'total_account' => 'COUNT(*)'
                                                  ])
                                              ->group('social_login_type')
                                              ->where('social_login_type in (?)',$this->socialLoginHelper->getAllTypesNotCheckEnable());
        $this->chartData = $this->socialAccountResource->getConnection()->fetchAll($select);
        return $this->chartData;
    }

    /**
     * @param $chartData
     * @return array
     */
    public function getChartColor()
    {
        $allColors = $this->socialLoginHelper->getChartColor();
        $chartColor = [];
        foreach ($this->getChartData() as $item) {
            $chartColor[] = ['color'=>$allColors[$item['social_login_type']]];
        }
        return $chartColor;
    }

}