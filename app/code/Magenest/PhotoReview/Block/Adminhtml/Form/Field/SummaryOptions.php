<?php

namespace Magenest\PhotoReview\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Summary Options
 */
class SummaryOptions extends AbstractFieldArray
{
    const SUMMARY_CODE = 'summary_code';
    const SUMMARY_LABEL = 'summary_label';

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            self::SUMMARY_CODE,
            [
                'label' => __('Summary Code'),
                'class' => 'required-entry'
            ]
        );
        $this->addColumn(
            self::SUMMARY_LABEL,
            [
                'label' => __('Summary Label'),
                'class' => 'required-entry'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
