<?php
namespace Magenest\Affiliate\Pricing\Render;

use Magenest\Affiliate\Model\ResourceModel\CatalogRule;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;

class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    /** @var \Magento\Framework\App\Http\Context */
    private $httpContext;

    /** @var CatalogRule */
    private $catalogRuleResource;

    /** @var TimezoneInterface */
    private $timezone;

    /**
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param CatalogRule $catalogRuleResource
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        \Magento\Framework\App\Http\Context $httpContext,
        CatalogRule $catalogRuleResource,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->httpContext = $httpContext;
        $this->catalogRuleResource = $catalogRuleResource;
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAffiliateRuleApplied()
    {
        $customerGroupId = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
        $websiteId = $this->_storeManager->getWebsite()->getId();

        $affiliateRule = $this->catalogRuleResource->getAffiliateRuleByProduct(
            $this->getSaleableItem()->getId(),
            $this->timezone->scopeTimeStamp(),
            $customerGroupId,
            $websiteId
        );

        return !empty($affiliateRule);
    }
}
