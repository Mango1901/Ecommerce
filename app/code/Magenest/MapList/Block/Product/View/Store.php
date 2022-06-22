<?php


namespace Magenest\MapList\Block\Product\View;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory;
use Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory as SourceItemFactory;

class Store extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var GetSourceItemsBySkuInterface
     */
    private $getSourceItemsBySku;

    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var CollectionFactory
     */
    protected $sourceCollectionFactory;

    /**
     * @var SourceItemFactory
     */
    protected $sourceItemCollectionFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        GetSourceItemsBySkuInterface $getSourceItemsBySku,
        SourceRepositoryInterface $sourceRepository,
        CollectionFactory $collectionFactory,
        SourceItemFactory $sourceCollectionFactory,
        array $data = []
    )
    {
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->sourceRepository = $sourceRepository;
        $this->sourceCollectionFactory = $collectionFactory;
        $this->sourceItemCollectionFactory = $sourceCollectionFactory;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    public function getStoreAvailable()
    {
        $product = $this->getProduct();
        if ($product->getId()) {
            $dataListSource = [];
            $sourceItems = $this->getSourceChildProduct($product);

            if (!empty($sourceItems)) {

                $sourceCollection = $this->sourceCollectionFactory->create();
                $sourceCollection->addFieldToFilter('source_code', ['neq' => 'default'])
                    ->addFieldToFilter('enabled', 1)
                    ->addFieldToFilter('visible', 1)
                    ->addFieldToFilter('source_code', $sourceItems);
                $dataListSource = $sourceCollection->getData();
            }
            return $dataListSource;
        }
    }

    public function getSourceChildProduct($product)
    {
        $sku = [$product->getSku()];
        if ($product->getTypeId() == 'configurable') {
            foreach ($product->getTypeInstance()->getUsedProducts($product) as $key => $_childProduct) {
                $sku[] = $_childProduct->getSku();
            }
        }

        $collection = $this->sourceItemCollectionFactory->create();
        $collection->addFieldToFilter('sku', $sku )
            ->addFieldToSelect('source_code')
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('quantity', ['gt' => 0])
            ->distinct(true);
        return $collection->getColumnValues('source_code');
    }

}
