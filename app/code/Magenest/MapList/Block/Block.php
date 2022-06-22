<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/21/16
 * Time: 08:35
 */

namespace Magenest\MapList\Block;

use Magenest\ShopByBrand\Model\Config\Router;
use Magento\Framework\Serialize\Serializer\Json;

class Block extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;

    protected $_scopeConfig;

    protected $_country;

    protected $_locationFactory;

    protected $_storeManagerInterface;

    protected $_holiday;

    protected $_holidayLocation;

    protected $_options;

    protected $_timezoneInterface;

    protected $_json;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        \Magenest\MapList\Model\Holiday $holiday,
        \Magenest\MapList\Model\ResourceModel\HolidayLocation\Collection $holidayLoction,
        \Magenest\MapList\Model\Config\Source\Options $options,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        Json $json
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_country = $country;
        $this->_locationFactory = $locationFactory;
        $this->_holiday = $holiday;
        $this->_holidayLocation = $holidayLoction;
        $this->_options = $options;
        $this->_storeManagerInterface = $storeManager;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_json = $json;
    }

    public function getConfig()
    {
        $dataConfig = array();
        $dataConfig['countryCode'] = $this->_scopeConfig->getValue('maplist/map/country');
        $dataConfig['mapApi'] = $this->_scopeConfig->getValue('maplist/map/api');
        $dataConfig['zoom'] = $this->_scopeConfig->getValue('maplist/map/zoom');
        $dataConfig['unit'] = $this->_scopeConfig->getValue('maplist/map/unit');
        $dataConfig['travel_mode'] = $this->_scopeConfig->getValue('maplist/map/travel_mode');
        $dataConfig['max_distance'] = $this->_scopeConfig->getValue('maplist/map/max_distance');
        $dataConfig['default_distance'] = $this->_scopeConfig->getValue('maplist/map/default_distance');
        $dataConfig['background_color'] = $this->_scopeConfig->getValue('maplist/general/wall_color');

        return $dataConfig;
    }

    protected function getImageUrl($imageData)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $image = $imageData;
        if (!$image) {
            return $this->getViewFileUrl('Magenest_MapList::images/logo-hoangphuc.png');
        }

        return $mediaDirectory . 'catalog/category/' . $image;
    }

    public function getMediaDirectory()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    protected function getGalleryImageUrl($imageData)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $image = $imageData;
        if (!$image) {
            return $this->getViewFileUrl('Magenest_MapList::images/storelocator-icon.png');
        }

        return $mediaDirectory . 'catalog/category/' . $image;
    }

    public function getHoliday($id)
    {
        return $this->_holiday->getResource()->getHoliday($id);
    }

    public function getPayments()
    {
        return $this->_options->getAllPaymentMethods();
    }

    public function getTime()
    {
        return $today = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
    }

    public function getAllHoliday()
    {
        $holidays = $this->_holidayLocation->getData();
        return $holidays;
    }

    //Add cslashes before ' and "
    public function addCslashes($str)
    {
        return addcslashes($str, "\0..\37\'\"\\");
    }

    /**
     * @param $data
     * @return array
     */
    public function jsondecodeHelper($data){
        try{
            return $this->_json->unserialize($data);
        }catch (\Exception $e){
            return array();
        }
    }

    /**
     * @param $data
     * @return bool|false|string
     */
    public function jsonEncodeHelper($data){
        try{
            return $this->_json->serialize($data);
        }catch (\Exception $e){
            return '';
        }
    }
}
