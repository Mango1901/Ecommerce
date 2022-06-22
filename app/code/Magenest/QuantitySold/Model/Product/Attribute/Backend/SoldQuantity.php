<?php
namespace Magenest\QuantitySold\Model\Product\Attribute\Backend;

use Magenest\QuantitySold\Setup\Patch\Data\AddSoldQuantityAttribute;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SoldQuantity extends AbstractBackend
{
    const PREVENT_BEFORE_SAVE = "prevent_before_save";

    /** @var LoggerInterface */
    private $logger;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\DataObject|\Magento\Catalog\Model\Product $object
     * @return SoldQuantity
     */
    public function beforeSave($object)
    {
        try {
            if (($object->dataHasChangedFor(AddSoldQuantityAttribute::UTILIZE_INITIAL_SOLD_QTY)
                || $object->dataHasChangedFor(AddSoldQuantityAttribute::SOLD_QTY))
                && empty($object->getData(self::PREVENT_BEFORE_SAVE))) {

                $soldQty = $object->getData(AddSoldQuantityAttribute::SOLD_QTY) ?? 0;

                if ($object->getData(AddSoldQuantityAttribute::UTILIZE_INITIAL_SOLD_QTY) == Status::STATUS_ENABLED) {
                    $initialQty = $this->scopeConfig->getValue(\Magenest\QuantitySold\Block\Product\SoldQuantity::INITIAL_SOLD);
                    $object->setData(AddSoldQuantityAttribute::FINAL_SOLD_QTY, $soldQty + $initialQty);
                } else {
                    $object->setData(AddSoldQuantityAttribute::FINAL_SOLD_QTY, $soldQty);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return parent::beforeSave($object);
    }
}
