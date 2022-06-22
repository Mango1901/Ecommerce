<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/16/16
 * Time: 21:55
 */

namespace Magenest\MapList\Block;

class Location extends Block
{
    public function getMap()
    {
        $mapData = new \stdClass();
        $mapData->map = null;
        $mapData->currentMenu = 'location';
        $mapData->config = $this->getConfig();
        $locationModel = $this->_coreRegistry->registry('maplist_location_model');

        try {
            $mapLocationData = $locationModel->getData();
            $mapLocationData['small_image_url'] = $this->getImageUrl($mapLocationData['small_image']);
            $mapLocationDataArr = array($mapLocationData);
            $mapData->location = $mapLocationDataArr;
        } catch (\Exception $e) {
            $mapData->location = array();
        }

        return $mapData;
    }
}
