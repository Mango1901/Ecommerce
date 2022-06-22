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
class MobileTemplate extends AbstractSource
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
				'value' => 0,
				'label' => __('Off Canvas Left'),
			],
			[
				'value' => 1,
				'label' => __('Accordion Menu'),
			],
			[
				'value' => 2,
				'label' => __('Custom Menu Alias'),
			],
			[
				'value' => 3,
				'label' => __('Drill Down Menu'),
			],
		];
	}
}
