<?php
/**
 * Copyright (c) Magenest, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\FacebookSupportLive\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Settings extends AbstractFieldArray
{
    protected $_optionrender;

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getOptionRenderer()
    {
        if (!$this->_optionrender) {
            $this->_optionrender = $this->getLayout()->createBlock(
                \Magenest\FacebookSupportLive\Block\Adminhtml\System\Config\Form\Field\Options::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true
                    ]
                ]
            );
            $this->_optionrender->setClass('order_status_select required-entry');
        }
        return $this->_optionrender;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('name', ['label' => __('Option'), 'class' => 'required-entry',  'renderer' => $this->_getOptionRenderer()]);
        $this->addColumn('value', ['label' => __('Value'), 'size' => '50px', 'class' => 'required-entry']);
        $this->_addAfter       = false;
        $this->_addButtonLabel = __('Add New Setting');
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_'.$this->_getOptionRenderer()->calcOptionHash($row->getData('name'))]='selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}