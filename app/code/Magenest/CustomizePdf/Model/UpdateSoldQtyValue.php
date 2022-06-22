<?php

namespace Magenest\CustomizePdf\Model;

use Magenest\CustomizePdf\Api\UpdateSoldQtyValueInterface;
use Magenest\QuantitySold\Setup\Patch\Data\AddSoldQuantityAttribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateSoldQtyValue
 */
class UpdateSoldQtyValue implements UpdateSoldQtyValueInterface
{
    const ATTRIBUTES_UPDATE = [AddSoldQuantityAttribute::FINAL_SOLD_QTY, AddSoldQuantityAttribute::SOLD_QTY];

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ResourceConnection $resourceConnection,
        ProductRepositoryInterface $productRepository,
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface $logger
    ) {
        $this->resourceConnection   = $resourceConnection;
        $this->productRepository    = $productRepository;
        $this->attributeRepository  = $attributeRepository;
        $this->logger               = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute($productId)
    {
        $result = [
            'success' => false
        ];

        try {
            $product = $this->productRepository->getById($productId);
            if ($product->getTypeId() === Type::TYPE_VIRTUAL) {
                $connection = $this->resourceConnection->getConnection();
                foreach (self::ATTRIBUTES_UPDATE as $item) {
                    $qty = $product->getCustomAttribute($item);
                    $newQty = $qty->getValue() + 1;
                    $attribute = $this->attributeRepository->get(
                        Product::ENTITY,
                        $item
                    );
                    $backendType = $attribute->getBackendType();
                    $mainTable = $this->resourceConnection->getTableName('catalog_product_entity_' . $backendType);
                    $connection->update(
                        $mainTable,
                        [
                            'value' => $newQty
                        ],
                        [
                            'entity_id = ?' => $productId,
                            'attribute_id = ?' => $attribute->getAttributeId()
                        ]
                    );
                    $result[$item] = $newQty;
                }
                $result['success'] = true;
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }
}
