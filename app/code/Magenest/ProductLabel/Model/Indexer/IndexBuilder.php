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

namespace Magenest\ProductLabel\Model\Indexer;

use Magenest\ProductLabel\Api\Data\ConstantInterface;
use Magenest\ProductLabel\Api\Data\LabelInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class IndexBuilder
 * @package Magenest\ProductLabel\Model\Indexer
 */
class IndexBuilder
{
    /**
     * @var int
     */
    protected $batchCount;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magenest\ProductLabel\Api\LabelRepositoryInterface
     */
    protected $labelRepository;

    /**
     * @var \Magenest\ProductLabel\Model\ResourceModel\LabelIndex
     */
    protected $indexResource;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\IndexBuilder\ProductLoader
     */
    private $productLoader;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * IndexBuilder constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository
     * @param \Magenest\ProductLabel\Model\ResourceModel\LabelIndex $labelIndex
     * @param \Magento\CatalogRule\Model\Indexer\IndexBuilder\ProductLoader $productLoader
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     * @param int $batchCount
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository,
        \Magenest\ProductLabel\Model\ResourceModel\LabelIndex $labelIndex,
        \Magento\CatalogRule\Model\Indexer\IndexBuilder\ProductLoader $productLoader,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        $batchCount = 1000
    ){
        $this->logger = $logger;
        $this->labelRepository = $labelRepository;
        $this->indexResource = $labelIndex;
        $this->productLoader = $productLoader;
        $this->batchCount = $batchCount;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
        $this->timezone = $timezone;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * Reindex by id
     *
     * @param int $id
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     */
    public function reindexByProductId($id)
    {
        $this->reindexByProductIds([$id]);
    }

    /**
     * Reindex by ids
     *
     * @param array $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function reindexByProductIds(array $ids)
    {
        try {
            $this->doReindexByProductIds($ids);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Magenest Product Label indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * Reindex by ids. Template method
     *
     * @param array $ids
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function doReindexByProductIds($ids)
    {
        foreach ($this->getActiveLabels() as $label) {
            if ($label->isInActiveDate()) {
                $this->reindexLabelProduct($label, $ids);
            }
        }

        return $this;
    }

    /**
     * @param null $ids
     * @param null $discountRule
     * @throws LocalizedException
     */
    public function reindexFull($ids = null, $discountRule = null)
    {
        try {
            if ($ids == null) {
                $this->cleanIndexFull();
            } else {
                $this->deleteIndexProduct($ids);
            }
            $this->doReindexFull($ids, $discountRule);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Magenest Product Label indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @param null $ids
     * @param null $discountRule
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function doReindexFull($ids = null, $discountRule = null)
    {
        foreach ($this->getActiveLabels() as $label) {
            if ($label->isInActiveDate()) {
                $this->reindexLabelProduct($label, $ids, $discountRule);
            }
        }

        return $this;
    }

    /**
     * @param $id
     * @throws LocalizedException
     */
    public function reindexByLabelId($id)
    {
        $this->reindexByLabelIds($id);
    }

    /**
     * @param $ids
     * @throws LocalizedException
     */
    public function reindexByLabelIds($ids)
    {
        try {
            $this->cleanByLabelIds($ids);
            $this->doReindexByLabelIds($ids);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(
                __("Magenest product label indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @param $ids
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function doReindexByLabelIds($ids)
    {
        foreach ($ids as $id) {
            $label = $this->labelRepository->get($id);
            if($label->getStatus() && $label->isInActiveDate()) {
                $this->reindexLabelProduct($label);
            }
        }
        return $this;
    }

    /**
     * @param \Magenest\ProductLabel\Model\Label $label
     * @param null $ids
     * @param null $discountRule
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function reindexLabelProduct(\Magenest\ProductLabel\Model\Label $label, $ids = null, $discountRule = null)
    {
        $rows = [];
        $connection = $this->indexResource->getConnection();
        $matchedProductIds = $label->getLabelMatchingProductIds($ids);
        $storeIds = $label->getStoreId();
        $customerGroupIds = $label->getCustomerGroupsIds();
        $indexTable = $this->indexResource->getMainTable();
        if ($storeIds && is_array($storeIds) && $matchedProductIds) {
            foreach ($matchedProductIds as $productId => $matchedStores) {
                $stores = array_intersect(array_keys($matchedStores), $storeIds);
                $dataProduct = $this->productRepository->getById($productId);
                if ($stores) {
                    foreach ($stores as $storeId) {
                        foreach ($customerGroupIds as $customerGroupId) {
                            $labelType = $label->getData('label_type');
                            switch ($labelType) {
                                case ConstantInterface::PRODUCT_LABEL_NEW_TYPE :
                                    $rows[] = $this->processLabelNew($dataProduct, $label, $productId, $storeId, $customerGroupId);
                                    break;
                                case ConstantInterface::PRODUCT_LABEL_SALE_TYPE :
                                    $rows[] = $this->processLabelSale($dataProduct, $label, $productId, $storeId, $customerGroupId, $labelType, $discountRule);
                                    break;
                                case ConstantInterface::PRODUCT_LABEL_BEST_SELLER :
                                    $bestSeller = $this->collectionFactory->create()->addFieldToFilter('product_id', $dataProduct->getEntityId());
                                    if ($bestSeller->count()) {
                                        $rows[] = $this->addData($productId, $label->getId(), $storeId, $customerGroupId);
                                    }
                                    break;
                                default :
                                    $rows[] = $this->addData($productId, $label->getId(), $storeId, $customerGroupId);
                                    break;
                            }
                        }
                    }
                }
            }
        }
        //Remove the empty data from the array
        $filterArray = array_filter($rows);
        if (!empty($filterArray)) {
            $connection->insertMultiple($indexTable, $filterArray);
        }
        return true;
    }

    /**
     * @param $dataProduct
     * @param $label
     * @param $productId
     * @param $storeId
     * @param $customerGroupId
     * @return array
     */
    public function processLabelNew($dataProduct, $label, $productId, $storeId, $customerGroupId) {
        $rows = [];
        $fromDateLabel = strtotime($label->getData('from_date'));
        $toDateLabel = strtotime($label->getData('to_date'));
        $fromDateProduct = strtotime($dataProduct->getNewsFromDate());
        $toDateProduct = strtotime($dataProduct->getNewsToDate());
        if ($fromDateLabel != null && $toDateLabel != null && $fromDateProduct !== null && $toDateProduct != null) {
            $checkTimeOverlap = ($fromDateProduct) <= ($toDateLabel) && ($fromDateLabel) < ($toDateProduct);
            if ($checkTimeOverlap) {
                $rows = $this->addData($productId, $label->getId(), $storeId, $customerGroupId);
            }
        } elseif ($fromDateLabel == null && $toDateLabel == null && $toDateProduct != null) {
            $todayDate = strtotime($this->timezone->date()->format('Y-m-d'));
            if ($toDateProduct >= $todayDate) {
                $rows = $this->addData($productId, $label->getId(), $storeId, $customerGroupId);
            }
        } elseif ($dataProduct->getNew() || $fromDateProduct) {
            $rows = $this->addData($productId, $label->getId(), $storeId, $customerGroupId);
        }
        return $rows;
    }

    /**
     * @param $dataProduct
     * @param $label
     * @param $productId
     * @param $storeId
     * @param $customerGroupId
     * @param $labelType
     * @param null $discountRule
     * @return array
     */
    public function processLabelSale($dataProduct, $label, $productId, $storeId, $customerGroupId, $labelType, $discountRule = null): array
    {
        $rows = [];
        $rule = $this->ruleFactory->create();
        $specialPrice = !empty($dataProduct->getData('special_price')) ? $dataProduct->getData('special_price') : '';
        $price = $dataProduct->getData('price');
        $regularPrice = $dataProduct->getPriceInfo()->getPrice('regular_price')->getValue();
        $finalPrice = $dataProduct->getPriceInfo()->getPrice('final_price')->getValue();
        $labelData = $label->getData();
        $dataCategory = isset($labelData['category_data']) ? $labelData['category_data'] : '';
        $discountAmount = $rule->calcProductPriceRule($dataProduct,$dataProduct->getPrice());

        if ($labelData != '') {
            $text = isset($dataCategory['text']) ? $dataCategory['text'] : '';
            $pattern = '/{{([a-zA-Z:\s]+)}}/';
            if ($text != '') {
                preg_match_all($pattern, $text, $variable);
            }
            $textLabel = isset($variable[1]) ? $variable[1] : '';
            if ($specialPrice != '' && $regularPrice != $finalPrice && $specialPrice != $price || $discountAmount !== null || $discountRule != null) {
                if ($textLabel != '') {
                    $type = reset($textLabel);
                    if ($type == ConstantInterface::AMOUNT || $type == ConstantInterface::PERCENT) {
                        $rows = $this->addData($productId, $label->getId(), $storeId, $customerGroupId);
                    }
                } else {
                    if ($labelType == ConstantInterface::LABEL_TYPE_IMAGE) {
                        $rows = $this->addData($productId, $label->getId(), $storeId, $customerGroupId);
                    }
                }
            }
        }
        return $rows;
    }

    /**
     * @param $productId
     * @param $labelId
     * @param $storeId
     * @param $customerGroupId
     * @return array
     */
    public function addData($productId, $labelId, $storeId, $customerGroupId) {
        $arr = [
            'product_id' => (int)$productId,
            'label_id' => $labelId,
            'store_id' => $storeId,
            'customer_group_id' => $customerGroupId
        ];
        return $arr;
    }

    /**
     * @return \Magenest\ProductLabel\Api\Data\LabelInterface[]
     */
    protected function getActiveLabels()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(LabelInterface::STATUS, 1)->create();
        $collection = $this->labelRepository->getList($searchCriteria);
        $labels = $collection->getItems();

        return $labels;
    }

    /**
     * @param $labelIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function cleanByLabelIds($labelIds)
    {
        $this->indexResource->cleanByLabelIds($labelIds);
    }

    /**
     * @throws LocalizedException
     */
    private function cleanIndexFull()
    {
        $this->indexResource->cleanIndexFull();
    }

    /**
     * @param $productId
     */
    public function deleteIndexProduct($productId) {
        $this->indexResource->deleteIndexProduct($productId);
    }
}
