<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 * Magenest_MembershipRP extension
 * NOTICE OF LICENSE
 * @category Magenest
 * @package  Magenest_MembershipRP
 */

namespace Magenest\RewardPoints\Block\Product;

use Magenest\RewardPoints\Helper\Data as Helper;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Load reward point block in pthml
 * Class PointBlock
 * @package Magenest\RewardPoints\Block\Product
 */
class PointBlock extends \Magento\Catalog\Block\Product\AbstractProduct
{
    const LAY0UT_CATALOG_PRODUCT_VIEW = 'catalog_product_view';
    const LAYOUT_CATALOG_CATEGORY_VIEW = 'catalog_category_view';
    const LAYOUT_CATALOGSEARCH_RESULT_INDEX = 'catalogsearch_result_index';
    const HOME_PAGE = 'cms_index_index';

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * PointBlock constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param Helper $_helper
     * @param CurrentCustomer $_currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        Helper $_helper,
        CurrentCustomer $_currentCustomer,
        array $data = []
    ) {
        $this->_helper = $_helper;
        $this->_currentCustomer = $_currentCustomer;
        parent::__construct($context, $data);
    }

    /**
     * @param $product
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPointBlock($product)
    {
        if (!$this->isPointShowable()) return '';

        $color = $this->_helper->getPointColor();
        $size = $this->_helper->getPointSize();
        $unit = $this->_helper->getPointUnit();

        $result = '';
        $point = $this->_helper->getProductPoints($product->getId());
        $upOrDown = $this->_helper->getUpOrDown();

        if ($this->_helper->getEnableModule()) {
            if ($product->getTypeId() == 'bundle') {
                $arrayPoint = ['min' => 0, 'max' => 0];
                if (is_array($point) and count($point) > 0) {
                    $arrayPoint = $this->_helper->getBundleProductPointsRange($product);
                }

                if ($arrayPoint['min'] && $arrayPoint['max']) {
                    if ($arrayPoint['min'] == $arrayPoint['max'])
                        $result = '<div class="price-box point-box"><strong><p style="color: ' . $color . '; font-size: ' . $size . 'px"> +' . $arrayPoint['min'] . ' ' . $unit . '</p></strong></div>';
                    else
                        $result = '<div class="price-box point-box"><strong><p style="color: ' . $color . '; font-size: ' . $size . 'px"> +' . $arrayPoint['min'] . ' to +' . $arrayPoint['max'] . ' ' . $unit . '</p></strong></div>';
                }
            } else {
                if ($upOrDown == 'up') {
                    $point = ceil($point);
                } else {
                    $point = floor($point);
                }
                if ($point > 0) {
                    if (in_array($product->getTypeId(), ['configurable', 'grouped'])) {
                        $result = '<div class="price-box point-box"><strong><p style="color: ' . $color . '; font-size: ' . $size . 'px">+ ' . __('From') . ' '. $point . ' ' . $unit . '</p></strong></div>';
                    } else {
                        $result = '<div class="price-box point-box"><strong><p style="color: ' . $color . '; font-size: ' . $size . 'px">+' . $point . ' ' . $unit . '</p></strong></div>';
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Check if reward points is showable in product detail and product list page
     *
     * @return bool
     */
    public function isPointShowable()
    {
        $fullActionName = $this->_request->getFullActionName();
        $isDisplayedInListPageEnaled = $this->_helper->isShowProductListEnabled();
        $isDisplayedInProductDetailEnabled = $this->_helper->isShowProductDetailEnabled();
        $isDisplayedInHomePageEnaled = $this->_helper->isShowPointInHomePageEnabled();
        if (empty($this->_currentCustomer->getCustomerId()) && !$this->_helper->isShowPointForGuest()) {
            return false;
        }
        if (!$isDisplayedInListPageEnaled && !$isDisplayedInProductDetailEnabled) return false;

        switch ($fullActionName) {
            case self::LAY0UT_CATALOG_PRODUCT_VIEW:
                if ($isDisplayedInProductDetailEnabled) return true;
                break;
            case self::LAYOUT_CATALOG_CATEGORY_VIEW:
                if ($isDisplayedInListPageEnaled) return true;
                break;
            case self::LAYOUT_CATALOGSEARCH_RESULT_INDEX:
                if ($isDisplayedInListPageEnaled) return true;
                break;
            case self::HOME_PAGE:
                if ($isDisplayedInHomePageEnaled) return true;
                break;
        }

        return false;
    }
}