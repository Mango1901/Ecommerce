<?php

namespace Magenest\AdminActivity\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class UserAgentColumn
 *
 * @package Magenest\AdminActivity\Ui\Component\Listing\Column
 */
class UserAgentColumn extends Column
{
	/**
	 * @var \Magenest\AdminActivity\Helper\Browser
	 */
	public $browser;

	/**
	 * UserAgentColumn constructor.
	 *
	 * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
	 * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
	 * @param \Magenest\AdminActivity\Helper\Browser $browser
	 * @param array $components
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\View\Element\UiComponent\ContextInterface $context,
		\Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
		\Magenest\AdminActivity\Helper\Browser $browser,
		array $components,
		array $data
	) {
		parent::__construct($context, $uiComponentFactory, $components, $data);
		$this->browser = $browser;
	}

	/**
	 * Get user agent data
	 *
	 * @param $item
	 * @return string
	 */
	public function getAgent($item)
	{
		$this->browser->reset();
		$this->browser->setUserAgent($item['user_agent']);
		return $this->browser->__toString();
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
				$item[$this->getData('name')] = $this->getAgent($item);
			}
		}

		return $dataSource;
	}
}
