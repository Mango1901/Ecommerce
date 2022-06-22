<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_RewardPoints
 */
namespace Magenest\RewardPoints\Block\Adminhtml\Transaction;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Title extends Column
{
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		array $components = [],
		array $data = []
	) {
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	public function prepareDataSource(array $dataSource)
	{
		parent::prepareDataSource($dataSource);
		if (isset($dataSource['data']['items'])) {
			foreach($dataSource['data']['items'] as & $item) {
				if (isset($item['rule_id'])) {
					switch ($item['rule_id']) {
						case 0:
							$item['title'] = __("Redeem points");
							break;
						case -1:
							$item['title'] = __("Points from admin");
							break;
						case -2:
							$item['title'] = __("Referral code points");
							break;
                        case -4:
                            $item['title'] = __("Deduct received points");
                            break;
                        case -5:
                            $item['title'] = __("Return applied points");
                            break;
					}
				}
				if (isset($item['title'])) {
					if ($item['comment'] == null) {
						$item['comment'] = $item['title'];
					}
				}
			}
		}
		return $dataSource;
	}
}
