<?php
namespace Magenest\OrderCancel\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magenest\OrderCancel\Block\Adminhtml\Form\Field\ApplyToColumn;

/**
 * Class CancelReasonOptions
 * Used for configuration admin setup Cancel Reason Options
 */
class CancelReasonOptions extends AbstractFieldArray
{
    /**
     * @var ApplyToColumn
     */
    private $applyToRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('cancel_reason_option', [
            'label' => __('Cancel Reason'),
            'class' => 'required-entry'
        ]);

        $this->addColumn('apply_to', [
            'label' => __('Apply To'),
            'renderer' => $this->getApplyToRenderer()
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $applyTo = $row->getApplyTo();
        if ($applyTo !== null) {
            $options['option_' . $this->getApplyToRenderer()->calcOptionHash($applyTo)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return ApplyToColumn
     * @throws LocalizedException
     */
    private function getApplyToRenderer()
    {
        if (!$this->applyToRenderer) {
            $this->applyToRenderer = $this->getLayout()->createBlock(
                ApplyToColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->applyToRenderer;
    }
}
