<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 14/06/2022
 * Time: 11:34
 */
declare(strict_types=1);

namespace Magenest\NotificationBox\Model;

use Magenest\NotificationBox\Api\IsProductHavePromotionInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class IsProductHavePromotion
 */
class IsProductHavePromotion implements IsProductHavePromotionInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * @Todo: Update follow by srs criteria
     * @inheritDoc
     */
    public function execute($product)
    {
        try {
            $price = $product->getPrice();
            $finalPrice = $product->getFinalPrice();
            $specialPrice = $product->getSpecialPrice();
            $specialPriceFromDate = $product->getSpecialFromDate();
            $specialPriceToDate = $product->getSpecialToDate();
            $today = $this->dateTime->timestamp();

            if (
                $specialPrice && $price > $finalPrice
            ) {
                if (
                    (!$specialPriceFromDate && !$specialPriceToDate) ||
                    (
                        $specialPriceFromDate &&
                        $specialPriceToDate &&
                        $today >= strtotime($specialPriceFromDate)
                        && $today <= strtotime($specialPriceToDate)
                    ) ||
                    (
                        $specialPriceFromDate &&
                        $today >= strtotime($specialPriceFromDate) &&
                        !$specialPriceToDate
                    )
                ) {
                    return true;
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return false;
    }
}
