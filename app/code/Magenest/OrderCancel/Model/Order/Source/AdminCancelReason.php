<?php

namespace Magenest\OrderCancel\Model\Order\Source;

use Magenest\OrderCancel\Block\Adminhtml\Form\Field\ApplyToColumn;

/**
 * Class AdminCancelReason
 */
class AdminCancelReason extends AbstractSource
{
    /**
     * @inheritDoc
     */
    protected function conditionGetReasonForArea($value)
    {
        return in_array($value, [ApplyToColumn::APPLY_TO_BOTH, ApplyToColumn::APPLY_TO_ADMIN]);
    }
}
