<?php

namespace Magenest\AdminActivity\Ui\Component\Listing\Column\ActionType;

/**
 * Class Options
 *
 * @package Magenest\AdminActivity\Ui\Component\Listing\Column\ActionType
 */
class Options implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @var \Magenest\AdminActivity\Helper\Data
	 */
	public $helper;

	/**
	 * Options constructor.
	 *
	 * @param \Magenest\AdminActivity\Helper\Data $helper
	 */
	public function __construct(\Magenest\AdminActivity\Helper\Data $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * List all option to get in filter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$data      = [];
		$lableList = $this->helper->getAllActions();
		foreach ($lableList as $key => $value) {
			$data[] = ['value' => $key, 'label' => __($value)];
		}
		return $data;
	}
}
