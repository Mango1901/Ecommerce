<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderExtraInformation\Block\Plugin\Order\Item\Renderer;

/**
 * Class DefaultRenderer
 * @package Magenest\OrderExtraInformation\Block\Plugin\Order\Item\Renderer
 */
class DefaultRenderer
{
    /**
     * After get item options
     *
     * @param \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject
     * @param $result
     * @return array
     */
    public function afterGetItemOptions(
        \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject,
        $result
    )
    {
        if($customerNote = $subject->getOrderItem()->getCustomerNote()){
            $result[] = [
                'label' => __('Note'),
                'value' => __($customerNote),
                'custom_view' => true,
            ];
        }

        return $result;
    }
}