<?php

namespace Magenest\AdminActivity\Ui\Component\Listing\Column;

use Magenest\AdminActivity\Helper\Data as Helper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ActionTypeColumn
 *
 * @package Magenest\AdminActivity\Ui\Component\Listing\Column
 */
class ActionTypeColumn extends Column
{
	/**
	 * @var \Magenest\AdminActivity\Helper\Data
	 */
	public $helper;

	/**
	 * ActionTypeColumn constructor.
	 *
	 * @param ContextInterface $context
	 * @param UiComponentFactory $uiComponentFactory
	 * @param Helper $helper
	 * @param array $components
	 * @param array $data
	 */
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		Helper $helper,
		array $components = [],
		array $data = []
	) {
		$this->helper = $helper;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource)
	{
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as & $item) {
				if (isset($item['action_type'])) {
					$item['action_type'] = $this->helper->getActionTranslatedLabel($item['action_type']);
				}
			}
		}
		return $dataSource;
	}
}
