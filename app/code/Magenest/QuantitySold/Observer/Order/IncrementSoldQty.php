<?php
namespace Magenest\QuantitySold\Observer\Order;

use Magenest\QuantitySold\Model\Product\Attribute\Backend\SoldQuantity as SoldQuantityBackend;
use Magenest\QuantitySold\Setup\Patch\Data\AddSoldQuantityAttribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;

class IncrementSoldQty implements ObserverInterface
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute(Observer $observer)
    {
        /** @var Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if (!$invoice->getIsUsedForRefund()) {
            foreach ($invoice->getItems() as $invoiceItem) {
                $product = $invoiceItem->getOrderItem()->getProduct();
                if (!$product->isComposite()) {
                    $origSoldQty = $product->getData(AddSoldQuantityAttribute::SOLD_QTY) ?? 0;
                    $origFinalSoldQty = $product->getData(AddSoldQuantityAttribute::FINAL_SOLD_QTY) ?? 0;

                    $product->setData(
                        AddSoldQuantityAttribute::SOLD_QTY,
                        $origSoldQty + $invoiceItem->getQty()
                    );
                    $product->setData(
                        AddSoldQuantityAttribute::FINAL_SOLD_QTY,
                        $origFinalSoldQty + $invoiceItem->getQty()
                    );
                    $product->setData(SoldQuantityBackend::PREVENT_BEFORE_SAVE, true);

                    $this->productRepository->save($product);
                }
            }
        }
    }
}
