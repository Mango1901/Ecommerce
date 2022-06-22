<?php
namespace Magenest\Affiliate\Pricing\Render;

use Magenest\Affiliate\Model\ResourceModel\CatalogRule;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;

class ConfigurablePriceBox extends \Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox
{
    /** @var TimezoneInterface */
    private $timezone;

    /** @var \Magento\Framework\App\Http\Context */
    private $httpContext;

    /** @var CatalogRule */
    private $catalogRuleResource;

    /**
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param TimezoneInterface $timezone
     * @param CatalogRule $catalogRuleResource
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param SalableResolverInterface $salableResolver
     * @param MinimalPriceCalculatorInterface $minimalPriceCalculator
     * @param ConfigurableOptionsProviderInterface $configurableOptionsProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        TimezoneInterface $timezone,
        CatalogRule $catalogRuleResource,
        \Magento\Framework\App\Http\Context $httpContext,
        SalableResolverInterface $salableResolver,
        MinimalPriceCalculatorInterface $minimalPriceCalculator,
        ConfigurableOptionsProviderInterface $configurableOptionsProvider,
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->httpContext = $httpContext;
        $this->catalogRuleResource = $catalogRuleResource;
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $salableResolver,
            $minimalPriceCalculator,
            $configurableOptionsProvider,
            $data
        );
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAffiliateRuleApplied()
    {
        $customerGroupId = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
        $websiteId = $this->_storeManager->getWebsite()->getId();

        $affiliateRule = $this->catalogRuleResource->getAffiliateRuleByProduct(
            $this->getChildProductIds(),
            $this->timezone->scopeTimeStamp(),
            $customerGroupId,
            $websiteId
        );

        return !empty($affiliateRule);
    }

    /**
     * @return array
     */
    private function getChildProductIds()
    {
        $parentId = $this->getSaleableItem()->getId();
        $children = $this->getSaleableItem()->getTypeInstance()->getChildrenIds($parentId);
        if (!empty($children)) {
            $children = array_merge(...$children);
        }
        $children[] = $parentId;

        return $children;
    }
}
