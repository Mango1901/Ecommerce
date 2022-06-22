<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Product;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Catalog\Product\ReviewInterface;

/**
 * Class Review
 * @package Magenest\MobileApi\Model\Catalog\Product
 */
class Review extends AbstractSimpleObject implements ReviewInterface
{
    /** @const */
    const KEY_TITLE = 'title';
    const KEY_NICKNAME = 'nickname';
    const KEY_DETAIL = 'detail';
    const KEY_QUALITY_RATING = 'quality_rating';
    const KEY_PRICE_RATING = 'price_rating';
    const KEY_RATING = 'rating';

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_get(self::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::KEY_TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getNickname()
    {
        return $this->_get(self::KEY_NICKNAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setNickname($nickname)
    {
        return $this->setData(self::KEY_NICKNAME, $nickname);
    }

    /**
     * {@inheritdoc}
     */
    public function getDetail()
    {
        return $this->_get(self::KEY_DETAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setDetail($detail)
    {
        return $this->setData(self::KEY_DETAIL, $detail);
    }

    /**
     * {@inheritdoc}
     */
    public function getQualityRating()
    {
        return $this->_get(self::KEY_QUALITY_RATING);
    }

    /**
     * {@inheritdoc}
     */
    public function setQualityRating($qualityRating)
    {
        return $this->setData(self::KEY_QUALITY_RATING, $qualityRating);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceRating()
    {
        return $this->_get(self::KEY_PRICE_RATING);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRating($priceRating)
    {
        return $this->setData(self::KEY_PRICE_RATING, $priceRating);
    }

    /**
     * @return mixed|null
     */
    public function getRating()
    {
        return $this->_get(self::KEY_RATING);
    }

    /**
     * @param $rating
     * @return mixed|void
     */
    public function setRating($rating)
    {
        return $this->setData(self::KEY_RATING, $rating);
    }
}
