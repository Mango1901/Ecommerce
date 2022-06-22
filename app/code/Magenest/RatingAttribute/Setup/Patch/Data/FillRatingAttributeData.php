<?php
namespace Magenest\RatingAttribute\Setup\Patch\Data;

use Magenest\RatingAttribute\Model\Constant;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Review\Model\Review;

class FillRatingAttributeData implements DataPatchInterface
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var Review */
    private $reviewModel;

    /** @var State */
    private $state;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Review $reviewModel
     * @param State $state
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Review $reviewModel,
        State $state
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->reviewModel = $reviewModel;
        $this->state = $state;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        $this->state->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'setDefaultAttributeValue']);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function setDefaultAttributeValue()
    {
        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($products as $product) {
            /** @var Product $product */
            $this->reviewModel->getEntitySummary($product);
            $product->setData(
                Constant::RATING_ATTRIBUTE,
                $product->getRatingSummary()->getRatingSummary() ?? 0
            );
            $this->productRepository->save($product);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [AddRatingAttribute::class];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
