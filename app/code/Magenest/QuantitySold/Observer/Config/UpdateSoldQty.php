<?php
namespace Magenest\QuantitySold\Observer\Config;

use Magenest\QuantitySold\Block\Product\SoldQuantity;
use Magenest\QuantitySold\Model\Product\Attribute\Backend\SoldQuantity as SoldQuantityBackend;
use Magenest\QuantitySold\Setup\Patch\Data\AddSoldQuantityAttribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class UpdateSoldQty implements ObserverInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $changedPath = $observer->getEvent()->getDataByKey('changed_paths');
            $websiteId = $observer->getEvent()->getDataByKey('website');
            if (in_array(SoldQuantity::INITIAL_SOLD, $changedPath) && !empty($websiteId)) {
                $newInitQty = $this->scopeConfig->getValue(SoldQuantity::INITIAL_SOLD);

                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter(AddSoldQuantityAttribute::UTILIZE_INITIAL_SOLD_QTY, Status::STATUS_ENABLED)
                    ->create();

                /** @var Product[] $products */
                $products = $this->productRepository->getList($searchCriteria)->getItems();
                foreach ($products as $product) {
                    $soldQty = $product->getData(AddSoldQuantityAttribute::SOLD_QTY) ?? 0;
                    $product->setData(AddSoldQuantityAttribute::FINAL_SOLD_QTY, $soldQty + $newInitQty);
                    $product->setData(SoldQuantityBackend::PREVENT_BEFORE_SAVE, true);
                    $this->productRepository->save($product);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }
}
