<?php
namespace Magenest\ViewStock\Setup\Patch\Data;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class FillAttributeData implements DataPatchInterface
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var State */
    private $state;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param State $state
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        State $state
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->state = $state;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        $this->state->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'setDefaultAttributeValue']);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function setDefaultAttributeValue()
    {
        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($products as $product) {
            /** @var Product $product */
            $product->setData(AddViewStockAttribute::VIEW_STOCK, Status::STATUS_DISABLED);
            $this->productRepository->save($product);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [AddViewStockAttribute::class];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
