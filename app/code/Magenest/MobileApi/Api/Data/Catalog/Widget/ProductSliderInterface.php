<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Catalog\Widget;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductSliderInterface
 * @package Magenest\MobileApi\Api\Data\Catalog\Widget
 */
interface ProductSliderInterface extends ExtensibleDataInterface
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
     * Get description
     *
     * @return string
     * @since 102.0.0
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get items list.
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getItems();

    /**
     * Set items list.
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get additional
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getAdditional();

    /**
     * Set additional
     *
     * @param \\Magenest\MobileApi\Api\Data\DataObjectInterface[] $additional
     * @return $this
     */
    public function setAdditional($additional);

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount();

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount);

    /**
     * Get show more url.
     *
     * @return string
     */
    public function getShowMoreUrl();

    /**
     * Set show more url
     *
     * @param string $url
     * @return $this
     */
    public function setShowMoreUrl($url);

    /**
     * Get category id.
     *
     * @return int
     */
    public function getCategoryId();

    /**
     * Set category id
     *
     * @param int $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * Get banner
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     */
    public function getBanners();

    /**
     * Set banner
     *
     * @param array $banner
     * @return $this
     */
    public function setBanners($banner);

    /**
     * Get type
     *
     * @return string
     * @since 102.0.0
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);
}
