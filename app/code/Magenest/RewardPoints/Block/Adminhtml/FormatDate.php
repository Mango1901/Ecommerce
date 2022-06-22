<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_RewardPoints
 */

namespace Magenest\RewardPoints\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class TitleRenderer
 * @package Magenest\RewardPoints\Block\Adminhtml
 */
class FormatDate extends AbstractRenderer
{
    /**
     * TitleRenderer constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $insertionDate = $row->getData('insertion_date');
        $dateFormat = date('Y-m-d', strtotime($insertionDate));
        return $dateFormat;
    }
}
