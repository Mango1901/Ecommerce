<?php
namespace Magenest\MobileApi\Model\Resolver\CmsBlockProductHomePage\Query;

use Magenest\MobileApi\Setup\Patch\Data\SuperSaleHomePage;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsCollection;
use Magento\CatalogWidget\Block\Product\ProductsListFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogGraphQl\Model\Resolver\Products\SearchResultFactory;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

class ProductQuery implements ProductQueryInterface
{
    /** @var CmsCollection */
    protected $blockCollectionFactory;

    /** @var ProductsListFactory */
    protected $productList;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var CollectionFactory  */
    protected $productCollectionFactory;

    /** @var SearchResultFactory  */
    private $searchResultFactory;

    /**
     * @var ReadExtensions
     */
    protected $_readExtensions;

    /**
     * @var JoinProcessorInterface
     */
    protected $_extensionAttributesJoinProcessor;

    public function __construct(
        CmsCollection $blockCollectionFactory,
        ProductsListFactory $productList,
        StoreManagerInterface $storeManager,
        CollectionFactory $productCollectionFactory,
        SearchResultFactory $searchResultFactory,
        ReadExtensions $readExtensions,
        JoinProcessorInterface $joinProcessor
    ) {
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->productList = $productList;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->_readExtensions = $readExtensions;
        $this->_extensionAttributesJoinProcessor = $joinProcessor;
    }

    public function getResult($args, $info, $context)
    {
        $identifier = SuperSaleHomePage::SUPER_SALE_HOME_PAGE_MOBILE;
        if (isset($args['identifier']) && $args['identifier'] != '') {
            $identifier = $args['identifier'];
        }
        $block = $this->blockCollectionFactory->create()
            ->addFieldToFilter(
                'identifier',
                $identifier
            )
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();


        $blockContentHtml = $block->getContent();

        $regex = '/(\w+)*?{{widget(.[^}}]+)/';
        $mainWidget = preg_match_all($regex, $blockContentHtml, $result);
        $categoryId = null;
        $productIds = [];
        if ($mainWidget && isset($result[0][0])) {
            $widgetParam = explode("conditions_encoded=", $result[0][0]);
            $conditions_encoded = end($widgetParam);

            if (!empty($conditions_encoded)) {
                $conditions = substr($conditions_encoded, 1, strpos($conditions_encoded, '^]^]') + strlen('^]^]') - 1);
                /* Get category of product */
                $categoryRegex = preg_match_all('/(\w+)*?category_ids(.[^\^\]]+)/', $conditions_encoded, $result_condition);
                if ($categoryRegex && isset($result_condition[0][0])) {
                    $catParam = explode("category_ids`,`value`:`", $result_condition[0][0]);
                    $catEncoded = end($catParam);
                    $categoryId = !empty($catEncoded) ? substr($catEncoded, 0, strlen($catEncoded) - 1 ) : '';
                }
                $productList = $this->productList->create();
                $productList->setData('conditions_encoded', $conditions);
                $productCollection = $productList->createCollection();
                $productIds = $productCollection->getAllIds();
            }
        }
        /* Get products count */
        $productsCount = '';

        /* Get sort order */
        $sortOrder= '';
        $sortOrderCondition = preg_match_all('/sort_order="(.[^\s]+)"/', $blockContentHtml, $sortOrderResult);
        if ($sortOrderCondition && isset($sortOrderResult[0][0])) {
            $sortOrder = substr($sortOrderResult[0][0], strlen('sort_order=') + 1, -1);
        }

        $storeId = $this->storeManager->getStore()->getId();
        if (!empty($productIds)) {
            $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
            $collection->addMinimalPrice()
                ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('*')
                ->addStoreFilter($storeId)->setPageSize($args['pageSize'])
                ->setCurPage($args['currentPage']);
            $this->processCollection($collection);
            $productArray = [];
            /** @var Product $product */
            $productsCount = 0;
            $items = $collection->getItems();
            foreach ($items as $product) {
                $productArray[$product->getId()] = $product->getData();
                $productArray[$product->getId()]['model'] = $product;
                $productsCount++;
            }
            if ($collection->getPageSize()) {
                $maxPages = (int)ceil($collection->getSize() / $collection->getPageSize());
            } else {
                $maxPages = 0;
            }
            return [
                'totalCount' => $collection->getSize(),
                'productsSearchResult' => $productArray,
                'pageSize' => $collection->getPageSize(),
                'currentPage' => $collection->getCurPage(),
                'totalPages' => $maxPages,
                'categoryId' => $categoryId,
                'block_id' => $block->getId(),
                'identifier' => $block->getIdentifier()
            ];
        }
    }

    /**
     * Process Collection
     * @param Collection $collection
     */
    public function processCollection(Collection &$collection)
    {
        $this->_extensionAttributesJoinProcessor->process($collection);
        $collection->addAttributeToSelect('*');
        $this->_addExtensionAttributes($collection);
    }

    /**
     * Add extension attributes to loaded items.
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function _addExtensionAttributes(Collection $collection)
    {
        foreach ($collection->getItems() as $item) {
            $this->_readExtensions->execute($item);
        }

        return $collection;
    }
}
