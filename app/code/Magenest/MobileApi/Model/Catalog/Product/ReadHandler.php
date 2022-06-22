<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Catalog\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magenest\MobileApi\Model\Catalog\ReviewFactory as ApiReviewFactory;
use Magenest\MobileApi\Model\Catalog\PricingFactory as ApiPricingFactory;
use Magenest\MobileApi\Model\Catalog\AttributeFactory as ApiAttributeFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Webapi\Model\Config\Converter;
use Magento\Webapi\Model\ConfigInterface as ModelConfigInterface;
use Magenest\MobileApi\Model\Catalog\Product\Type\OptionsInterface;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Catalog\Block\Product\View\Attributes as AttributesBlock;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Output;
use Magenest\MobileApi\Model\Helper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magenest\MobileApi\Api\Data\Promotion\GiftInterfaceFactory;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;

/**
 * Class ReadHandler
 * @package Magenest\MobileApi\Model\Product
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ApiReviewFactory
     */
    protected $_apiReviewDataFactory;

    /**
     * @var ApiPricingFactory
     */
    protected $_apiPricingDataFactory;

    /**
     * @var ApiAttributeFactory
     */
    protected $_apiAttributeDataFactory;

    /**
     * @var ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var ReviewCollectionFactory
     */
    protected $_reviewCollectionFactory;

    /**
     * @var OptionsInterface[]
     */
    protected $_productOptionsReader;

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var AttributesBlock
     */
    protected $_attributesBlock;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Output
     */
    protected $_outPutHelper;

    /**
     * @var Helper
     */
    protected $_apiHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var GiftInterfaceFactory
     */
    protected $_giftFactory;

    /**
     * @var AssetRepository
     */
    protected $_assetRepo;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Emulation
     */
    protected $_appEmulation;

    /**
     * @var StockItemRepositoryInterface
     */
    protected $stockItemRepositoryInterface;

    /**
     * @param ApiReviewFactory $apiReviewFactory
     * @param ApiPricingFactory $apiPricingFactory
     * @param ApiAttributeFactory $apiAttributeFactory
     * @param ReviewFactory $reviewFactory
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param ModelConfigInterface $config
     * @param FilterProvider $filterProvider
     * @param AttributesBlock $attributesBlock
     * @param Registry $registry
     * @param Output $outputHelper
     * @param Helper $apiHelper
     * @param PriceCurrencyInterface $priceCurrency
     * @param GiftInterfaceFactory $giftFactory
     * @param AssetRepository $assetRepo
     * @param StoreManagerInterface $storeManager
     * @param Emulation $emulation
     * @param array $productOptionsReader
     * @param StockItemRepositoryInterface $stockItemRepositoryInterface
     */
    public function __construct(
        ApiReviewFactory $apiReviewFactory,
        ApiPricingFactory $apiPricingFactory,
        ApiAttributeFactory $apiAttributeFactory,
        ReviewFactory $reviewFactory,
        ReviewCollectionFactory $reviewCollectionFactory,
        ModelConfigInterface $config,
        FilterProvider $filterProvider,
        AttributesBlock $attributesBlock,
        Registry $registry,
        Output $outputHelper,
        Helper $apiHelper,
        PriceCurrencyInterface $priceCurrency,
        GiftInterfaceFactory $giftFactory,
        AssetRepository $assetRepo,
        StoreManagerInterface $storeManager,
        Emulation $emulation,
        $productOptionsReader = [],
        StockItemRepositoryInterface $stockItemRepositoryInterface
    )
    {
        $this->_apiReviewDataFactory = $apiReviewFactory;
        $this->_apiPricingDataFactory = $apiPricingFactory;
        $this->_apiAttributeDataFactory = $apiAttributeFactory;
        $this->_reviewFactory = $reviewFactory;
        $this->_reviewCollectionFactory = $reviewCollectionFactory;
        $this->_productOptionsReader = $productOptionsReader;
        $this->_filterProvider = $filterProvider;
        $this->_attributesBlock = $attributesBlock;
        $this->_coreRegistry = $registry;
        $this->_outPutHelper = $outputHelper;
        $this->_apiHelper = $apiHelper;
        $this->_priceCurrency = $priceCurrency;
        $this->_giftFactory = $giftFactory;
        $this->_assetRepo = $assetRepo;
        $this->_storeManager = $storeManager;
        $this->_appEmulation = $emulation;
        $this->stockItemRepositoryInterface = $stockItemRepositoryInterface;
    }

    /**
     * @inheritdoc
     */
    public function execute($product, $arguments = [])
    {
        /** @var ProductInterface $product */
        if ($this->_apiHelper->isRestApiGet()) {
            /** Add information */
            $this->_addGeneralInformation($product);
            $this->_addDetailInformation($product);
            /** Add information by product type */
            if (isset($this->_productOptionsReader[$product->getTypeId()])) {
                $this->_productOptionsReader[$product->getTypeId()]->process($product);
            }
        }

        return $product;
    }

    /**
     * Add general information
     *
     * @param ProductInterface $product
     */
    private function _addGeneralInformation($product)
    {
        /** Reviews */
        $this->_addReviews($product);
        $extensionAttributes = $product->getExtensionAttributes();
        /** Is new */
        $extensionAttributes->setIsNew((strtotime($product->getCreatedAt()) >= strtotime('-500 days')));
        $product->setExtensionAttributes($extensionAttributes);
        /** Pricing */
        $finalPrice = $product->getPriceInfo()->getPrice('final_price');
        $regularPrice = $product->getPriceInfo()->getPrice('regular_price');

        $pricing = $this->_apiPricingDataFactory->create()
            ->setFinalPrice($finalPrice->getAmount()->getValue())
            ->setRegularPrice($regularPrice->getAmount()->getValue());
        $extensionAttributes->setPricing($pricing);

        try {
            $productStockItem = $this->stockItemRepositoryInterface->get($product->getId());
            $isBackOrder = $productStockItem->getBackorders() != "0" ? true : false;
            $extensionAttributes->setIsBackorder($isBackOrder);
        }catch (\Exception $exception) {
        }

        $product->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Add detail information
     *
     * @param ProductInterface $product
     */
    private function _addDetailInformation($product)
    {
        if (!$this->_apiHelper->isDetailRequest()) {
            return;
        }

        $this->_appEmulation->startEnvironmentEmulation($this->_storeManager->getStore()->getId(), Area::AREA_FRONTEND, true);
        /** ====================== Extension attributes ====================== */
        $this->_addAttributes($product);
        /** ====================== Custom ====================== */
        if ($product->getCustomAttribute('description')) {
            $product->setCustomAttribute('description', $this->_filterProvider->getPageFilter()->filter($product->getCustomAttribute('description')->getValue()));
        }

        $this->_appEmulation->stopEnvironmentEmulation();
    }

    /**
     * Add attributes
     *
     * @param ProductInterface $product
     */
    private function _addAttributes($product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        $this->_coreRegistry->unregister('product');
        $this->_coreRegistry->register('product', $product);
        $attributes = [];

        foreach ($this->_attributesBlock->getAdditionalData() as $attribute) {
            $attributes[] = $this->_apiAttributeDataFactory->create()
                ->setLabel($attribute['label'])
                ->setValue($this->_outPutHelper->productAttribute($this->_attributesBlock->getProduct(), $attribute['value'], $attribute['code']));
        }

        $extensionAttributes->setAttributes($attributes);
        $product->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Add reviews
     *
     * @param ProductInterface $product
     */
    private function _addReviews($product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        $this->_reviewFactory->create()->getEntitySummary($product, $product->getStoreId());
        $reviews = [];
        $reviewSummary[] = [
            'review_counts' => $product->getRatingSummary()->getReviewsCount(),
            'rating_summary' => $product->getRatingSummary()->getRatingSummary()
        ];

        if ($this->_apiHelper->isDetailRequest()) {
            $reviewCollection = $this->_reviewCollectionFactory->create()
                ->addStoreFilter($product->getStoreId())
                ->addEntityFilter('product', $product->getId())
                ->setDateOrder()
                ->addStatusFilter(1)
                ->addRateVotes();

            foreach ($reviewCollection->getItems() as $_review) {
                $reviews[$_review->getReviewId()] = [
                    'title' => $_review->getTitle(),
                    'detail' => $_review->getDetail(),
                    'nickname' => $_review->getNickname(),
                    'created_at' => $_review->getCreatedAt()
                ];

                foreach ($_review->getRatingVotes() as $_vote) {
                    $reviews[$_review->getReviewId()]['votes'][] = ['rating_code' => $_vote->getRatingCode(), 'percent' => $_vote->getPercent()];
                }
            }
        }

        $review = $this->_apiReviewDataFactory->create()
            ->setReviews($reviews)
            ->setReviewSummary($reviewSummary);
        $extensionAttributes->setReview($review);
        $product->setExtensionAttributes($extensionAttributes);
    }
}
