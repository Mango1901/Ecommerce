<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 10/01/2019
 * Time: 9:41
 */

namespace Magenest\ViettelPost\Block\Adminhtml\System\Config\Fieldset;

class SenderAddress extends \Magento\Config\Block\System\Config\Form\Field
{
    const PROVINCE_DATA_CACHE_ID = 'PROVINCE_COLLECTION_DATA';
    const DISTRICT_DATA_CACHE_ID = 'DISTRICT_COLLECTION_DATA';
    const WARDS_DATA_CACHE_ID = 'WARDS_COLLECTION_DATA';

    protected $provinceCollectionFactory;

    protected $districtCollectionFactory;

    protected $wardsCollectionFactory;

    protected $provinceFactory;

    protected $districtFactory;

    protected $wardsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param array $data
     */
    public function __construct(
        \Magenest\ViettelPost\Model\ResourceModel\Province\CollectionFactory $provinceCollectionFactory,
        \Magenest\ViettelPost\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        \Magenest\ViettelPost\Model\ResourceModel\Wards\CollectionFactory $wardsCollectionFactory,
        \Magenest\ViettelPost\Model\ProvinceFactory $provinceFactory,
        \Magenest\ViettelPost\Model\DistrictFactory $districtFactory,
        \Magenest\ViettelPost\Model\WardsFactory $wardsFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->provinceCollectionFactory = $provinceCollectionFactory;
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->wardsCollectionFactory = $wardsCollectionFactory;
        $this->provinceFactory = $provinceFactory;
        $this->districtFactory = $districtFactory;
        $this->wardsFactory = $wardsFactory;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('Magenest_ViettelPost::system/config/fieldset/sender_address.phtml');
        }

        return $this;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getProvinceData(){
        $data = $this->_cache->load(self::PROVINCE_DATA_CACHE_ID);
        if(!$data){
            $dataDB = $this->provinceCollectionFactory->create()
                ->addFieldToSelect(["province_id","province_name"])
                ->getData();
            $data = $dataDB;
            $this->_cache->save(json_encode($dataDB), self::PROVINCE_DATA_CACHE_ID);
        }else{
            $data = json_decode($data, true);
        }
        return $data;
    }

    public function getDistrictData(){
        $data = $this->_cache->load(self::DISTRICT_DATA_CACHE_ID);
        if(!$data){
            $dataDB = $this->districtCollectionFactory->create()
                ->addFieldToSelect(["district_id","province_id","district_name"])
                ->getData();
            $data = $dataDB;
            $this->_cache->save(json_encode($dataDB), self::DISTRICT_DATA_CACHE_ID);
        }else{
            $data = json_decode($data, true);
        }
        return $data;
    }

    public function getWardsData(){
        $data = $this->_cache->load(self::WARDS_DATA_CACHE_ID);
        if(!$data){
            $dataDB = $this->wardsCollectionFactory->create()
                ->addFieldToSelect(["wards_id","district_id", "wards_name"])
                ->getData();
            $data = $dataDB;
            $this->_cache->save(json_encode($dataDB), self::WARDS_DATA_CACHE_ID);
        }else{
            $data = json_decode($data, true);
        }
        return $data;
    }

    public function getCurrentProvince(){
        $provinceId = $this->_scopeConfig->getValue('carriers/viettelpost/information/sender_province');
        $provinceName = $this->provinceFactory->create()->load($provinceId)->getProvinceName();
        return [
            'value' => $provinceId,
            'label' => $provinceName
        ];
    }

    public function getCurrentDistrict(){
        $districtId = $this->_scopeConfig->getValue('carriers/viettelpost/information/sender_district');
        $districtName = $this->districtFactory->create()->load($districtId)->getDistrictName();
        return [
            'value' => $districtId,
            'label' => $districtName
        ];
    }

    public function getCurrentWards(){
        $wardsId = $this->_scopeConfig->getValue('carriers/viettelpost/information/sender_wards');
        $wardsName = $this->wardsFactory->create()->load($wardsId)->getWardsName();
        return [
            'value' => $wardsId,
            'label' => $wardsName
        ];
    }
}
