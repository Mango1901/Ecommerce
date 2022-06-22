<?php
namespace Magenest\QuantitySold\Model\Product\Attribute\Backend;

use Magenest\QuantitySold\Setup\Patch\Data\AddSoldQuantityAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;

class TrueSoldQuantity extends AbstractBackend
{
    /**
     * @param \Magento\Framework\DataObject|\Magento\Catalog\Model\Product $object
     * @return TrueSoldQuantity
     * @throws LocalizedException
     */
    public function beforeSave($object)
    {
        $qty = (int)$object->getData(AddSoldQuantityAttribute::SOLD_QTY);
        if (!is_int($qty) || $qty < 0) {
            throw new LocalizedException(__("Invalid input value for sold quantity."));
        }

        return parent::beforeSave($object);
    }
}
