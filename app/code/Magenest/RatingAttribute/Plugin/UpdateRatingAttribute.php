<?php
namespace Magenest\RatingAttribute\Plugin;

use Magenest\RatingAttribute\Model\Constant;
use Magenest\RatingAttribute\Setup\Patch\Data\AddRatingAttribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Review\Model\Review;

class UpdateRatingAttribute
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Review $review
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function afterAggregate($review, $result)
    {
        try {
            /** @var Product $product */
            $product = $this->productRepository->getById($review->getEntityPkValue());

            $review->getEntitySummary($product);
            $product->setData(
                Constant::RATING_ATTRIBUTE,
                $product->getRatingSummary()->getRatingSummary() ?? 0
            );
            $product->setData(
                Constant::RATING_OPTION_ATTRIBUTE,
                floor(($product->getRatingSummary()->getRatingSummary() ?? 100)/20)
            );
            $this->productRepository->save($product);
        } catch (\Exception $exception) {
        }

        return $result;
    }
}
