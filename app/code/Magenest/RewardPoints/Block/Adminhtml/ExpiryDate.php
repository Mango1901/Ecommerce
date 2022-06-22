<?php
declare(strict_types=1);

/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Booking & Reservation extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 20/01/2021 10:50
 */

namespace Magenest\RewardPoints\Block\Adminhtml;

use Magenest\RewardPoints\Helper\Data;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class ExpiryDate extends AbstractRenderer
{
    /**
     * @var Data
     */
    protected $_pointHelper;

    /**
     * ExpiryDate constructor.
     * @param Data $pointHelper
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        Data $pointHelper,
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_pointHelper = $pointHelper;
    }

    public function render(DataObject $row)
    {
        if ($row->getData('points_change') > 0 && !$this->_pointHelper->getExpiryType($row->getData('id'))) {
            return __('Never');
        }
        return empty($row->getData('expired_date')) ? __('No expiry date') : date('Y-m-d', strtotime($row->getData('expired_date')));
    }
}
