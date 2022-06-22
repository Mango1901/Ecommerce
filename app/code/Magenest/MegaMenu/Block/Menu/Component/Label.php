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

class Label extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Element\BlockInterface
{
	protected $_template = 'Magenest_MegaMenu::menu/component/label.phtml';

	protected $labelFactory;

	protected $labelResource;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magenest\MegaMenu\Model\ResourceModel\Label $labelResource,
		\Magenest\MegaMenu\Model\LabelFactory $labelFactory,
		array $data = []
	) {
		$this->labelFactory = $labelFactory;
		$this->labelResource = $labelResource;
		parent::__construct($context, $data);
	}

	public function getLabelModel()
	{
		$labelModel = $this->labelFactory->create();
		if ($this->getLabelId()) {
			$this->labelResource->load($labelModel, $this->getLabelId());
		}

		return $labelModel;
	}

	public function getLabelId()
	{
		return $this->getData('label');
	}
}
