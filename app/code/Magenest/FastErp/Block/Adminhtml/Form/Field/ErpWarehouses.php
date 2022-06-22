<?php

namespace Magenest\FastErp\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ErpWarehouses
 */
class ErpWarehouses extends AbstractFieldArray
{
    const COLUMN_ERP_SOURCE_NAME = 'erp_source_name';
    const COLUMN_ERP_SOURCE_CODE = 'erp_source_code';

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn(self::COLUMN_ERP_SOURCE_NAME, ['label' => __('Erp Source Name'), 'class' => 'required-entry']);
        $this->addColumn(self::COLUMN_ERP_SOURCE_CODE, ['label' => __('Erp Source Code'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
