<?php

namespace Magenest\MapList\Model\Product\Attribute\Backend;

class Stores extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $_storeProduct;

    protected $metadataPool;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magenest\MapList\Model\StoreProductFactory $storeProductFactory
     */
    public function __construct(
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magenest\MapList\Model\StoreProductFactory $storeProductFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->metadataPool = $metadataPool;
        $this->_storeProduct = $storeProductFactory;
    }

    /**
     * Before Attribute Import Process
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'stores') {
            $data = $object->getData($attributeCode);
            if (!is_array($data)) {
                $data = array();
            }

            $object->setData($attributeCode, implode(',', $data) ?: null);
        }

        if (!$object->hasData($attributeCode)) {
            $object->setData($attributeCode, null);
        }

        return $this;
    }

    /**
     * After Load Attribute Process
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
        $entityId = $entityMetadata->getLinkField();
        $productId = $object->getData($entityId);
        $productCollection = $this->_storeProduct->create()->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->getData();
        $attributeCode = $this->getAttribute()->getName();
        $data = array();
        if (!empty($productCollection)) {
            $index = 0;
            foreach ($productCollection as $product) {
                $data[$index] = $product['location_id'];
                $index++;
            }

            $object->setData($attributeCode, implode(',', $data));
        } else {
            $object->setData($attributeCode, null);
        }

        return $this;
    }
}
