<?php
namespace Magenest\CustomizePdf\Controller\SoldQty;

use Magenest\QuantitySold\Block\Product\SoldQuantity;
use Magenest\QuantitySold\Setup\Patch\Data\AddSoldQuantityAttribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Get
 * Used for get sold qty for virtual product
 */
class Get implements ActionInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        RequestInterface           $request,
        JsonFactory                $resultJsonFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->_request          = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson   = $this->resultJsonFactory->create();
        $dataResponse = [
            'success' => false
        ];
        if ($this->_request->isAjax()) {
            $postValue = $this->_request->getPostValue();
            if ($productId = $postValue['productId']) {
                $product = $this->productRepository->getById($productId);
                if ($product->getTypeId() === Type::TYPE_VIRTUAL) {
                    $soldQty                  = $product->getCustomAttribute(AddSoldQuantityAttribute::SOLD_QTY);
                    $dataResponse['success']  = true;
                    $dataResponse['sold_qty'] = SoldQuantity::numberPrefixEncode((int)$soldQty->getValue());
                }
            }
        }
        return $resultJson->setData($dataResponse);
    }
}
