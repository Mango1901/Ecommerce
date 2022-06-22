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

namespace Magenest\ProductLabel\Model;

use Magenest\ProductLabel\Api\Data\LabelInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magenest\ProductLabel\Api\LabelRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LabelRepository implements LabelRepositoryInterface
{

    /**
     * @var LabelFactory
     */
    private $labelModel;

    /**
     * @var ResourceModel\Label
     */
    private $labelResource;

    /**
     * @var ResourceModel\Label\CollectionFactory
     */
    private $labelCollection;

    /**
     * @var \Magenest\ProductLabel\Api\Data\LabelSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var array
     */
    private $label = [];

    /**
     * LabelRepository constructor.
     * @param LabelFactory $labelFactory
     * @param ResourceModel\Label $labelResource
     * @param ResourceModel\Label\CollectionFactory $collection
     * @param \Magenest\ProductLabel\Api\Data\LabelSearchResultsInterfaceFactory $searchResultsInterfaceFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        \Magenest\ProductLabel\Model\LabelFactory $labelFactory,
        \Magenest\ProductLabel\Model\ResourceModel\Label $labelResource,
        \Magenest\ProductLabel\Model\ResourceModel\Label\CollectionFactory $collection,
        \Magenest\ProductLabel\Api\Data\LabelSearchResultsInterfaceFactory $searchResultsInterfaceFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->labelModel = $labelFactory;
        $this->labelResource = $labelResource;
        $this->labelCollection = $collection;
        $this->searchResultsFactory = $searchResultsInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(LabelInterface $label)
    {
        if ($label->getId()) {
            $label = $this->get($label->getId())->addData($label->getData());
        }
        try {
            $this->labelResource->save($label);
            unset($this->label[$label->getId()]);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('The label couldn\'t be saved.'), $e);
        }
        return $label;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if (!isset($this->label[$id])) {
            /** @var \Magenest\ProductLabel\Model\Label $label */
            $label = $this->labelModel->create();

            $this->labelResource->load($label, $id);
            if (!$label->getId()) {
                throw new NoSuchEntityException(
                    __('The rule with the "%1" ID wasn\'t found. Verify the ID and try again.', $id)
                );
            }
            $this->label[$id] = $label;
        }
        return $this->label[$id];
    }

    /**
     * @inheritdoc
     */
    public function delete(LabelInterface $label)
    {
        try {
            $this->labelResource->delete($label);
            unset($this->label[$label->getId()]);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('The "%1" rule couldn\'t be removed.', $label->getId()));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $model = $this->get($id);
        $this->delete($model);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Magenest\ProductLabel\Model\ResourceModel\Label\Collection $collection */
        $collection = $this->createCollection();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults->setTotalCount($collection->getSize());
        $labels = [];
        foreach ($collection->getItems() as $label) {
            $labels[] = $this->get($label->getLabelId());
        }
        $searchResults->setItems($labels);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function createNewObject()
    {
        return $this->labelModel->create();
    }

    /**
     * @inheritdoc
     */
    public function createCollection()
    {
        return $this->labelCollection->create();
    }
}
