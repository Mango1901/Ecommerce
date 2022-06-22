<?php

namespace Magenest\AdminActivity\Api\Data;

/**
 * Interface LogSearchResultsInterface
 *
 * @package Magenest\EnhancedSMTP\Api\Data
 */
interface ActivitySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
	/**
	 * Get admin activity list.
	 *
	 * @return \Magenest\AdminActivity\Model\Activity[]
	 * @api
	 */
	public function getItems();

	/**
	 * Set admin activity list.
	 *
	 * @param \Magenest\AdminActivity\Model\Activity[] $items
	 * @return $this
	 * @api
	 */
	public function setItems(array $items);
}
