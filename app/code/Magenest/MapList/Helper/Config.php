<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 11/22/16
 * Time: 09:49
 */

namespace Magenest\MapList\Helper;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\Helper\Context;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_encryptor;

    public function __construct(
        Context $context,
        EncryptorInterface $encryptor
    ) {
        $this->_encryptor = $encryptor;
        parent::__construct($context);
    }

    public function isShowMapListItem()
    {
        return $this->scopeConfig->getValue(
            'maplist/general/show_item_section',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getShowTab()
    {
        return $this->scopeConfig->getValue(
            'maplist/general/show_tab',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getZoom()
    {
        return $this->scopeConfig->getValue(
            'maplist/map/zoom',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            'maplist/map/api',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getOpenHours(){
        $arrTime1 = [];
        $arrTime1['opening_hours_sunday'] = $this->scopeConfig->getValue(
                'maplist/default_opening_time/default_opening_time_sunday',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        $arrTime1['opening_hours_monday'] = $this->scopeConfig->getValue(
            'maplist/default_opening_time/default_opening_time_monday',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $arrTime1['opening_hours_tuesday'] = $this->scopeConfig->getValue(
            'maplist/default_opening_time/default_opening_time_tuesday',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $arrTime1['opening_hours_wednesday'] = $this->scopeConfig->getValue(
            'maplist/default_opening_time/default_opening_time_wednesday',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $arrTime1['opening_hours_thursday'] = $this->scopeConfig->getValue(
            'maplist/default_opening_time/default_opening_time_thursday',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $arrTime1['opening_hours_friday'] = $this->scopeConfig->getValue(
            'maplist/default_opening_time/default_opening_time_friday',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $arrTime1['opening_hours_saturday'] = $this->scopeConfig->getValue(
            'maplist/default_opening_time/default_opening_time_saturday',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $arrTime = json_encode($arrTime1);
    }
}
