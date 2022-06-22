<?php
namespace Magenest\Directory\Block\Adminhtml\Area\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Area
 */
class Area extends AbstractFieldArray
{
    const COLUMN_AREA_LABEL = 'area_label';
    const COLUMN_AREA_CODE = 'area_code';
    const COLUMN_AREA_ID = 'area_id';
    const COLUMN_AREA_CUSTOMER_ID = 'area_customer_id';

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn(self::COLUMN_AREA_LABEL, ['label' => __('Label'), 'class' => 'required-entry']);
        $this->addColumn(self::COLUMN_AREA_CODE, ['label' => __('Code'), 'class' => 'required-entry']);
        $this->addColumn(self::COLUMN_AREA_CUSTOMER_ID, ['label' => __('Area Customer ID'), 'class' => 'required-entry']);
        $this->addColumn(self::COLUMN_AREA_ID, ['label' => __('Area Customer ID'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
