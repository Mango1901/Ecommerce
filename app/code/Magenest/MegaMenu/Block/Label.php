<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class Label extends Template
{

	protected $registry;

	/**
	 * Label constructor.
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param array $data
	 */
	public function __construct(
		Template\Context $context,
		\Magento\Framework\Registry $registry,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->registry = $registry;
	}

	public function getLabel()
	{
		$model = $this->registry->registry('magenest_menu_label');
		if ($model->getLabelId()) {
			return $model->getToHtml();
		} else {
			return '<label id="megamenu-label"
              class="megamenu-label label-position-top"><span class="label-text"></span><span class="label-arrow"></span></label>';
		}
	}
}
