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

namespace Magenest\MegaMenu\Block\Menu\Component;

class Side extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Element\BlockInterface
{
	protected $_helper;

	protected $_template = 'Magenest_MegaMenu::menu/component/side.phtml';

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magenest\MegaMenu\Helper\Data $_helper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->_helper = $_helper;
	}

	public function getHelper()
	{
		return $this->_helper;
	}
}
