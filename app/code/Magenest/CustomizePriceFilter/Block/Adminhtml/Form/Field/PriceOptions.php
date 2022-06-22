<?php

namespace Magenest\CustomizePriceFilter\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class PriceOptions extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('price_option', [
            'label' => __('Price'),
            'class' => 'required-entry validate-number validate-number-range number-range-1-9999999999999999'
        ]);
        $this->addColumn('price_option_label', [
            'label' => __('Price label'),
            'class' => 'required-entry'
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
