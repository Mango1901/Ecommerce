<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 29/10/2021
 * Time: 16:11
 */

namespace Magenest\RatingAttribute\Setup\Patch\Data;

use Magenest\RatingAttribute\Model\Attribute\Source\StarOptions;
use Magenest\RatingAttribute\Model\Constant;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Review\Model\Review;

class AddRatingOptionAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $setup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var Review */
    private $reviewModel;

    /** @var State */
    private $state;

    /**
     * AddRatingOptionAttribute constructor.
     * @param ModuleDataSetupInterface $setup
     * @param EavSetupFactory $eavSetupFactory
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Review $reviewModel
     * @param State $state
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        EavSetupFactory $eavSetupFactory,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Review $reviewModel,
        State $state
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->reviewModel = $reviewModel;
        $this->state = $state;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $this->addRatingAttribute($eavSetup);
    }

    /**
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addRatingAttribute(EavSetup $eavSetup)
    {
        $config = [
            'type' => 'int',
            'label' => 'Rating Option',
            'input' => 'select',
            'required' => false,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_in_advanced_search' => true,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'source' => StarOptions::class,
            'default' => 5,
            'is_filterable' => 2
        ];
        if (!$eavSetup->getAttribute(Product::ENTITY, Constant::RATING_OPTION_ATTRIBUTE)) {
            $eavSetup->addAttribute(Product::ENTITY, Constant::RATING_OPTION_ATTRIBUTE, $config);
        } else {
            $eavSetup->updateAttribute(Product::ENTITY, Constant::RATING_OPTION_ATTRIBUTE, $config);
        }
        $eavSetup->updateAttribute(Product::ENTITY, Constant::RATING_OPTION_ATTRIBUTE, 'is_filterable', 2);

        $this->state->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'setProductRatingOptions']);
    }

    public function setProductRatingOptions()
    {

        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($products as $product) {
            /** @var Product $product */
            $this->reviewModel->getEntitySummary($product);
            $product->setData(
                Constant::RATING_OPTION_ATTRIBUTE,
                floor(($product->getRatingSummary()->getRatingSummary() ?? 100)/20)
            );
            $this->productRepository->save($product);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}

