<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Api;

/**
 * @api
 */
interface LabelRepositoryInterface
{
    /**
     * Create or update a product label
     *
     * @param \Magenest\ProductLabel\Api\Data\LabelInterface $label
     * @return \Magenest\ProductLabel\Api\Data\LabelInterface
     */
    public function save(\Magenest\ProductLabel\Api\Data\LabelInterface $label);

    /**
     * Get Label By ID
     *
     * @param int $id
     * @return \Magenest\ProductLabel\Api\Data\LabelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * Delete Label
     *
     * @param \Magenest\ProductLabel\Api\Data\LabelInterface $label
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Magenest\ProductLabel\Api\Data\LabelInterface $label);

    /**
     * Delete Label By ID
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Get label list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magenest\ProductLabel\Api\Data\LabelSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get New Label Object
     *
     * @return \Magenest\ProductLabel\Api\Data\LabelInterface
     */
    public function createNewObject();

    /**
     * Get New Label Collection
     *
     * @return \Magenest\ProductLabel\Api\Data\LabelInterface[] Array of items.
     */
    public function createCollection();
}
