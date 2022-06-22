<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\MegaMenu\Model\Export\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * @inheritdoc
 */
class MenuTemplate extends AbstractSource
{
	/**
	 * Retrieve All options
	 *
	 * @return array
	 */
	public function getAllOptions()
	{
		return [
			[
				'value' => 'vertical_left',
				'label' => __('Vertical Menu Left'),
			],
			[
				'value' => 'vertical_right',
				'label' => __('Vertical Menu Right'),
			],
			[
				'value' => 'horizontal',
				'label' => __('Horizontal Menu'),
			]
		];
	}
}
