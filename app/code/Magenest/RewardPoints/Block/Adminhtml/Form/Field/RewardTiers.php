<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Form\Field;

/**
 * Class RewardTiers
 * @package Magenest\RewardPoints\Block\Adminhtml\Form\Field
 */
class RewardTiers extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_template = 'Magenest_RewardPoints::system.phtml';

    /**
     *
     */
    protected function _prepareToRender()
    {
        $this->addColumn('points', ['label' => __('Points'), 'class' => 'validate-greater-than-zero validate-number required-entry input-text admin__control-text']);
        $this->addColumn('discount', ['label' => __('Discount %'), 'class' => 'validate-greater-than-zero validate-number required-entry input-text admin__control-text validate-number-range number-range-0.00-100.00']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplateTemp($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);

        return '<input type="text" id="' . $this->_getCellInputElementId(
                '<%- _id %>',
                $columnName
            ) .
            '"' .
            ' name="' .
            $inputName .
            '" value="" ' .
            ($column['size'] ? 'size="' .
                $column['size'] .
                '"' : '') .
            ' class="' .
            (isset(
                $column['class']
            ) ? $column['class'] : 'input-text') . '"' . (isset(
                $column['style']
            ) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}
