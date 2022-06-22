<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 17/11/2020 15:00
 */

namespace Magenest\RewardPoints\Ui\Component\Listing\Customer;

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Helper\ImageHelper;
use Magenest\RewardPoints\Helper\MembershipAction;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CustomerTier extends Column
{
    /**
     * @var MembershipAction
     */
    protected $_membershipAction;

    /**
     * CustomerTier constructor.
     * @param MembershipAction $membershipAction
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        MembershipAction $membershipAction,
        ContextInterface $context, UiComponentFactory $uiComponentFactory, array $components = [], array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_membershipAction = $membershipAction;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($this->getData('name') == 'customer_tier') {
                    $tier = $this->_membershipAction->setCustomerId($item['entity_id'])->getTierByCustomerId();
                    if ($tier->getId()) {
                        $item[$this->getData('name')] = $tier->getData(MembershipInterface::GROUP_NAME);
                    } else {
                        $item[$this->getData('name')] = __('Not yet join');
                    }
                }
            }
        }
        return $dataSource;
    }
}