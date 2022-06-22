<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ReadonlyField extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setReadonly(true);
        $element->setClass('readonly-field');
        return parent::_getElementHtml($element);
    }
}
