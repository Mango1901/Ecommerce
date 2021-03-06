<?php

namespace Magenest\GiaoHangTietKiem\Helper;

use Magento\Customer\Model\Data\Address;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Dir\Reader;
use Zend\Config\Reader\Json;

class CityProvinceProvider extends AbstractHelper
{
    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @var Json
     */
    protected $jsonReader;

    /**
     * @var Reader
     */
    protected $moduleReader;

    protected $cities;

    public function __construct(
        Context $context,
        Reader $moduleReader,
        Json $jsonReader,
        DirectoryHelper $directoryHelper
    ){
        $this->directoryHelper = $directoryHelper;
        $this->jsonReader = $jsonReader;
        $this->moduleReader = $moduleReader;
        parent::__construct($context);
        $this->initData();
    }

    protected function initData()
    {
        $directory = $this->moduleReader->getModuleDir('view', 'Magenest_GiaoHangTietKiem');
        $jsonFile = $directory."/frontend/web/json/location_converted.json";
        $this->cities = $this->jsonReader->fromFile($jsonFile);
        foreach($this->cities as $key => &$city) {
            foreach ($city['district'] as &$district) {
                $district['code'] = $district['district_code'];
            }
        }
    }

    public function getCity()
    {
        if ($this->cities == null) {
            $this->initData();
        }
        return $this->getCityOptions($this->cities);
    }

    public function getDistrict()
    {
        if ($this->cities == null) {
            $this->initData();
        }
        return $this->getDistrictOptions($this->cities);
    }

    public function getDistrictJson()
    {
        $data = $this->getDistrictOptionsJson();
        $data['config'] = [
            'show_all_regions' => true,
            'regions_required' => array_map('strval',array_keys($this->cities))
        ];
        return json_encode($data);
    }

    public function getDistrictOptionsJson()
    {
        $data = [];
        foreach($this->cities as $key => $city) {
            if ($key == 771) {
                $quanData = [];
                $huyenData = [];
                foreach ($city['district'] as &$district) {
                    if(strpos($district['name_with_type'], 'Qu???n') !== false) {
                        $quanData[] = $district;
                    } else {
                        $huyenData[] = $district;
                    }
                }
                usort($quanData, function ($a, $b) {
                    return strnatcmp($this->stripVN($a['name']), $this->stripVN($b['name']));
                });
                usort($huyenData, function ($a, $b) {
                    return strnatcmp($this->stripVN($a['name']), $this->stripVN($b['name']));
                });
                unset($district);
                foreach ($quanData as &$district) {
                    $district['name'] = $district['name_with_type'];
                }
                unset($district);
                foreach ($huyenData as &$district) {
                    $district['name'] = $district['name_with_type'];
                }
                $city['district'] = array_merge($quanData, $huyenData);
            }
            $data[(string)$key] = $city['district'];
        }
        return $data;
    }

    public function getCityOptions($cities)
    {
        $options[] = [
            'value' => '',
            'label' => __('Please select your city')
        ];
        $options[] = [
            'value' => "722",
            'label' => $cities[722]['name'],
        ];
        $options[] = [
            'value' => "771",
            'label' => $cities[771]['name'],
        ];
        unset($cities[722]);
        unset($cities[771]);
        foreach ($cities as &$city) {
            $city['default_name'] = str_replace("T???nh", "", $city['default_name']);
            $city['default_name'] = str_replace("Th??nh ph???", "", $city['default_name']);
        }
        usort($cities, function ($a, $b) {
            return strnatcmp($this->stripVN($a['default_name']), $this->stripVN($b['default_name']));
        });
        unset($city);
        foreach ($cities as $city) {
            $options[] = [
                'value' => $city['region_id'],
                'label' => $city['name'],
            ];
        }

        return $options;
    }

    public function getDistrictOptions($cities)
    {
        $options[] = [
            'value' => '',
            'label' => __('Please select your district')
        ];
        foreach ($cities as $city) {
            foreach ($city['district'] as $key => &$district) {
                $district['value'] = $key;
            }
            usort($city['district'], function ($a, $b) {
                return strnatcmp($this->stripVN($a['name']), $this->stripVN($b['name']));
            });
            unset($district);
            foreach ($city['district'] as $key => $district) {
                if ($city['region_id'] == 771) {
                    $district['name'] = $district['name_with_type'];
                }
                $options[$district['value']] = [
                    'value' => $district['value'],
                    'label' => $district['name'],
                    'city_id' => $city['region_id'],
                    'title' => $district['name']
                ];
            }
        }
        return $options;
    }

    public function getCityById($id)
    {
        $length = strlen($id);
        for ($i = 0; $i < 3 - $length; $i++) {
            $id = "0".$id;
        }

        return isset($this->cities[$id]) ? $this->cities[$id] : false;
    }

    public function getProvinceById($cityId, $provinceId)
    {
        $city = $this->getCityById($cityId);
        $length = strlen($provinceId);
        for ($i = 0; $i < 3 - $length; $i++) {
            $provinceId = "0".$provinceId;
        }
        if ($city) {
            return isset($city['district'][$provinceId]) ? $city['district'][$provinceId] : false;
        }
        return false;
    }

    public function stripVN($str) {
        $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'a', $str);
        $str = preg_replace("/(??|??|???|???|???|??|???|???|???|???|???)/", 'e', $str);
        $str = preg_replace("/(??|??|???|???|??)/", 'i', $str);
        $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'o', $str);
        $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???)/", 'u', $str);
        $str = preg_replace("/(???|??|???|???|???)/", 'y', $str);
        $str = preg_replace("/(??)/", 'd', $str);

        $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'A', $str);
        $str = preg_replace("/(??|??|???|???|???|??|???|???|???|???|???)/", 'E', $str);
        $str = preg_replace("/(??|??|???|???|??)/", 'I', $str);
        $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'O', $str);
        $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???)/", 'U', $str);
        $str = preg_replace("/(???|??|???|???|???)/", 'Y', $str);
        $str = preg_replace("/(??)/", 'D', $str);
        if (is_numeric($str))
            return (int)$str;
        return $str;
    }

    public function addressConverter(Address $address)
    {
        $street = implode(", ",$address->getStreet());
        $city = $address->getCity();
        $regionName = $address->getRegion()->getRegion();
        if (is_numeric($city)) {
            $city = $this->getCityById($city);
            if ($city) {
                $region = $this->getProvinceById($city['region_id'], $address->getRegion()->getRegionId());
                $city = $city['default_name'];
                $regionName = $region['name_with_type'];
            }
        }

        return "{$street}, {$regionName}, $city, Vi???t Nam";
    }

    public function getCities()
    {
        return $this->cities;
    }

}
