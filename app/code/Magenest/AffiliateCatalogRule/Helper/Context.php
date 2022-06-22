<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 28/10/2021
 * Time: 14:52
 */

namespace Magenest\AffiliateCatalogRule\Helper;

use Magenest\Core\Helper\CatalogHelper;

class Context extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    protected $catalogHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        CatalogHelper $catalogHelper
    ) {
        $this->httpContext = $httpContext;
        $this->catalogHelper = $catalogHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isAffiliate()
    {
        return $this->httpContext->getValue(Constant::IS_AFFILIATE_CONTEXT);
    }

    /**
     * @param $finalPrice
     * @param $regularPrice
     * @return string
     */
    public function getDiscountBlockHtml($finalPrice, $regularPrice)
    {
        $discountPercent = round((1 - $finalPrice / $regularPrice) * 100, 0);
        if ($discountPercent) {
            return
                "<span data-label=' ". __('reduction') .
                    "' class='discount-item' style='display: none'><span class='discount-circle'></span>" .
                    $discountPercent . "%" .
                "</span>";
        }
        return '';
    }

    public function getDiscountBlockHtmlByProduct($product)
    {
        $discountPercent = $this->catalogHelper->getDiscountPercent($product);
        if ($discountPercent) {
            return
                "<span data-label=' ". __('reduction') .
                "' class='discount-item' style='display: none'><span class='discount-circle'></span>" .
                $discountPercent . "%" .
                "</span>";
        }
        return '';
    }
}
