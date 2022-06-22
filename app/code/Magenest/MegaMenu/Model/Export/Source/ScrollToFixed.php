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
class ScrollToFixed extends AbstractSource
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
				'label' => __('Enabled'),
			],
			[
				'value' => 1,
				'label' => __('Disabled'),
			],
		];
	}
}
