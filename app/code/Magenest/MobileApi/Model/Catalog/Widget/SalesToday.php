<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Widget;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magenest\MobileApi\Model\Catalog\WidgetAbstract;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsCollection;
use Magenest\MobileApi\Setup\Patch\Data\SaleToday;
use Magento\CatalogWidget\Block\Product\ProductsList;

class SalesToday extends WidgetAbstract
{
    /**
     * @var ProductSliderFactory
     */
    protected $_productSliderFactory;

    /**
     * @var  CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ProductResource
     */
    protected $_resource;

    /** @var CmsCollection */
    protected $blockCollectionFactory;

    /** @var ProductsList */
    protected $productList;

    /**
     * Constructor.
     *
     * @param ReadExtensions $readExtensions
     * @param JoinProcessorInterface $joinProcessor
     * @param CollectionFactory $productCollectionFactory
     * @param ProductSliderFactory $productSliderFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductResource $resource
     * @param CmsCollection $blockCollectionFactory
     * @param ProductsList $productList
     * @param array $data
     */
    function __construct(
        ReadExtensions $readExtensions,
        JoinProcessorInterface $joinProcessor,
        CollectionFactory $productCollectionFactory,
        ProductSliderFactory $productSliderFactory,
        StoreManagerInterface $storeManager,
        ProductResource $resource,
        CmsCollection $blockCollectionFactory,
        ProductsList $productList,
        array $data
    )
    {
        $this->_productSliderFactory = $productSliderFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->productList = $productList;
        parent::__construct($readExtensions, $joinProcessor, $data);
    }

    /**
     * Render Product Slider Sales Today
     */
    public function render()
    {
        $cms_block_sale_today_id = SaleToday::SALE_TODAY_CMS_BLOCK_ID;
        $block_content_html = $this->blockCollectionFactory->create()
                                                           ->addFieldToFilter('identifier', $cms_block_sale_today_id)
                                                           ->setCurPage(1)
                                                           ->setPageSize(1)
                                                           ->getFirstItem()
                                                           ->getContent();
        $regex = '/(\w+)*?{{widget(.[^}}]+)/';
        $mainWidget = preg_match_all($regex, $block_content_html, $result);
        $saleTodayItems = [];

        if ($mainWidget && isset($result[0][0])) {
            $widgetParam = explode("conditions_encoded=", $result[0][0]);
            $conditions_encoded = end($widgetParam);

            if (!empty($conditions_encoded)) {
                $conditions = substr($conditions_encoded, 1, strpos($conditions_encoded, '^]^]') + strlen('^]^]') - 1);

                $this->productList->setData('conditions_encoded', $conditions);
                $productCollection = $this->productList->createCollection();
                $saleTodayItems = $productCollection->load()->getItems();
            }
        }

        $data = $this->getData();
        $storeId = $this->_storeManager->getStore()->getId();
        $productIds = [];
        foreach ($saleTodayItems as $product) {
            $productIds[] = $product->getId();
        }
        if (!empty($productIds)) {
            $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
            $collection->addMinimalPrice()
                       ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                       ->addFinalPrice()
                       ->addTaxPercents()
                       ->addAttributeToSelect('*')
                       ->addStoreFilter($storeId);
            $this->processCollection($collection);
            return $this->_productSliderFactory->create()
                                               ->setTitle($data['title'])
                                               ->setItems(array_map(function ($id) use ($collection) {
                                                   return $collection->getItemById($id);
                                               }, $productIds))
                                               ->setTotalCount($collection->getSize())
                                               ->setType('product_slider');
        } else {
            return $this->_productSliderFactory->create()
                                               ->setTitle($data['title'])
                                               ->setItems([])
                                               ->setTotalCount(0)
                                               ->setType('product_slider');
        }
    }
}
