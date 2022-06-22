<?php

namespace Magenest\RewardPoints\Plugin;

use Magenest\RewardPoints\Helper\Data;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magenest\RewardPoints\Block\Product\PointBlock;

/**
 * Class PointDetail
 * @package Magenest\RewardPoints\Plugin
 */
class PointDetail
{
    const IS_RENDER_REWARD_POINT = 'is_render_reward';

    /**
     * @var PointBlock
     */
    protected $block;

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * PointDetail constructor.
     *
     * @param PointBlock $block
     * @param CurrentCustomer $currentCustomer
     * @param Data $helper
     */
    public function __construct(
        PointBlock $block,
        CurrentCustomer $currentCustomer,
        Data $helper

    ) {
        $this->block            = $block;
        $this->_helper          = $helper;
        $this->_currentCustomer = $currentCustomer;

    }

    /**
     * @param \Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param $result
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetProductPrice(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        $result,
        \Magento\Catalog\Model\Product $product
    ) {
        if (!$product->getData(self::IS_RENDER_REWARD_POINT)) {
            $point = $this->block->getPointBlock($product);
            if (!empty($point)) {
                $result = $result . $point;
                $product->setData(self::IS_RENDER_REWARD_POINT, true);
            }
        }
        return $result;
    }

    /**
     * @param \Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param $result
     * @param \Magento\Catalog\Model\Product $product
     * @param string $renderZone
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetProductPriceHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        $result,
        \Magento\Catalog\Model\Product $product,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
    ) {
        if (!$product->getData(self::IS_RENDER_REWARD_POINT)) {
            $point = $this->block->getPointBlock($product);
            if (!empty($point)) {
                $result = $result . $point;
                $product->setData(self::IS_RENDER_REWARD_POINT, true);
            }
        }
        return $result;
    }
}
