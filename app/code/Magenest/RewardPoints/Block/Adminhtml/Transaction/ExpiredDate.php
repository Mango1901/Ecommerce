<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 29/12/2020 10:05
 */

namespace Magenest\RewardPoints\Block\Adminhtml\Transaction;

use Magenest\RewardPoints\Block\Customer\Points;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ExpiredDate extends Column
{
    /**
     * @var Points
     */
    protected $_pointBlock;

    public function __construct(
        Points $pointBlock,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_pointBlock = $pointBlock;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['expired_date'])) {
                    if ($item['points_change'] > 0 && !$this->_pointBlock->getExpiryType($item['id'])) {
                        $item['expired_date'] = __('Never');
                    }
                }
            }
        }
        return $dataSource;
    }
}
