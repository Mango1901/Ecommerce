<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Catalog\Product;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReviewInterface
 * @package Magenest\MobileApi\Api\Data\Catalog\Product
 */
interface ReviewInterface extends ExtensibleDataInterface
{
    /**
     * Get title
     *
     * @return string
     * @since 102.0.0
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get nickname
     *
     * @return string
     * @since 102.0.0
     */
    public function getNickname();

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return $this
     */
    public function setNickname($nickname);

    /**
     * Get detail
     *
     * @return string
     * @since 102.0.0
     */
    public function getDetail();

    /**
     * Set detail
     *
     * @param string $detail
     * @return $this
     */
    public function setDetail($detail);

    /**
     * Get quality rating
     *
     * @return string
     * @since 102.0.0
     */
    public function getQualityRating();

    /**
     * Set quality rating
     *
     * @param string $qualityRating
     * @return $this
     */
    public function setQualityRating($qualityRating);

    /**
     * Get price rating
     *
     * @return string
     * @since 102.0.0
     */
    public function getPriceRating();

    /**
     * Set price rating
     *
     * @param string $priceRating
     * @return $this
     */
    public function setPriceRating($priceRating);

    /**
     * @return mixed
     */
    public function getRating();

    /**
     * @param $rating
     * @return mixed
     */
    public function setRating($rating);
}
