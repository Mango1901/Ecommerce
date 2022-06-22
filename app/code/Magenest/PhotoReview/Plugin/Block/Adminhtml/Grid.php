<?php

namespace Magenest\PhotoReview\Plugin\Block\Adminhtml;

use Magenest\PhotoReview\Block\Adminhtml\Grid\Filter\Title;

/**
 * Plugin Grid
 */
class Grid
{
    public function beforeAddColumn(\Magento\Review\Block\Adminhtml\Grid $subject, $columnId, $column)
    {
        if ($columnId === 'title') {
            $column = [
                'header' => __('Title'),
                'type' => 'select',
                'filter_index' => 'rdt.title',
                'index' => 'title',
                'filter' => Title::class,
                'renderer' => \Magenest\PhotoReview\Block\Adminhtml\Grid\Renderer\Title::class
            ];
        }
        return [$columnId, $column];
    }
}
