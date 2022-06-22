<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/23/16
 * Time: 15:13
 */

namespace Magenest\MapList\Block\Adminhtml\Template;

class GoogleMap extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $html = '<div id="map" style="height: 400px; position: relative;"></div>';

        return $html;
    }
}
