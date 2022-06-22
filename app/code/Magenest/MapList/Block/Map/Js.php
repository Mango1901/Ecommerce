<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/23/16
 * Time: 13:50
 */

namespace Magenest\MapList\Block\Map;

use Magenest\MapList\Block\Block;
use Magenest\MapList\Helper\Constant;

class Js extends Block
{
    public function getDataView()
    {
        $dataView = $this->getConfig();

        return $dataView;
    }
    public function getImageUrl($imageData)
    {
        return parent::getImageUrl($imageData);
    }

    public function getImageMarker()
    {
        return $this->getViewFileUrl('Magenest_MapList::images/marker.png');
    }
}
