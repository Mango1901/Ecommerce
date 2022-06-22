<?php
namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab\Renderer;
use Magento\Framework\DataObject;
use Magento\Backend\Block\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class ProductId extends AbstractRenderer
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * ProductId constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Context $context,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function render(DataObject $row)
    {
        $html = '';
        $sku = $row->getData($this->getColumn()->getIndex());
        if (isset($sku)) {
            $product = $this->productRepository->get($sku);
            if ($product->getId()) {
                $productId = $product->getId();
                $url = $this->getUrlProduct($productId);
                $html = "<a href='" . $url . "' target='_blank'>$productId</a>";
            }
        }

        return $html;
    }
    /**
     * @param $productId
     *
     * @return string
     */
    private function getUrlProduct($productId)
    {
        return $this->getUrl(
            'catalog/product/edit', ['id' => $productId]
        );
    }
}
