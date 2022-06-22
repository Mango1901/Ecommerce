<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/23/16
 * Time: 15:13
 */

namespace Magenest\MapList\Block\Adminhtml\Template;

class Address extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $addressLocation = $this->getValue();
        $html =
            '<input id="location_address" name="address" data-ui-id="maplist-location-add-opening-hours-fieldset-element-text-address"  title="address" type="text" class=" input-text admin__control-text">';

        return $html;
    }
}
