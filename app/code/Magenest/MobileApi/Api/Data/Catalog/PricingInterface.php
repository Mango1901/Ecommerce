<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Catalog;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReviewInterface
 * @package Magenest\MobileApi\Api\Data\Catalog
 */
interface PricingInterface extends ExtensibleDataInterface
{
    /**
     * Get regular price
     *
     * @return float
     * @since 102.0.0
     */
    public function getRegularPrice();

    /**
     * Get regular price
     *
     * @param int|float|double $regularPrice
     * @return $this
     */
    public function setRegularPrice($regularPrice);

    /**
     * Get regular minimal price
     *
     * @return float
     * @since 102.0.0
     */
    public function getRegularMinimalPrice();

    /**
     * Get regular minimal price
     *
     * @param float $regularMinimalPrice
     * @return $this
     */
    public function setRegularMinimalPrice($regularMinimalPrice);

    /**
     * Get regular maximal price
     *
     * @return float
     * @since 102.0.0
     */
    public function getRegularMaximalPrice();

    /**
     * Get regular maximal price
     *
     * @param float $regularMaximalPrice
     * @return $this
     */
    public function setRegularMaximalPrice($regularMaximalPrice);

    /**
     * Get final price
     *
     * @return float
     * @since 102.0.0
     */
    public function getFinalPrice();

    /**
     * Get final price
     *
     * @param float $finalPrice
     * @return $this
     */
    public function setFinalPrice($finalPrice);

    /**
     * Get final minimal price
     *
     * @return float
     * @since 102.0.0
     */
    public function getFinalMinimalPrice();

    /**
     * Get final minimal price
     *
     * @param float $finalMinimalPrice
     * @return $this
     */
    public function setFinalMinimalPrice($finalMinimalPrice);

    /**
     * Get regular maximal price
     *
     * @return float
     * @since 102.0.0
     */
    public function getFinalMaximalPrice();

    /**
     * Get regular maximal price
     *
     * @param float $finalMaximalPrice
     * @return $this
     */
    public function setFinalMaximalPrice($finalMaximalPrice);
}
