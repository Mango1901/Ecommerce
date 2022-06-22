<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\OrderExtraInformation\Block\Adminhtml\Plugin\Items\Column;

/**
 * Class Name
 * @package Magenest\OrderExtraInformation\Block\Adminhtml\Plugin\Items\Column
 */
class Name
{
    /**
     * After get order options
     *
     * @param \Magento\Sales\Block\Adminhtml\Items\Column\Name $subject
     * @param $result
     * @return array
     */
    public function afterGetOrderOptions(
        \Magento\Sales\Block\Adminhtml\Items\Column\Name $subject,
        $result
    )
    {
        if($customerNote = $subject->getItem()->getCustomerNote()){
            $result[] = [
                'label' => __('Note'),
                'value' => __($customerNote),
                'custom_view' => true,
            ];
        }

        return $result;
    }
}