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
class Event extends AbstractSource
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
				'value' => 'hover',
				'label' => __('Hover'),
			],
			[
				'value' => 'click',
				'label' => __('Click'),
			],
		];
	}
}
