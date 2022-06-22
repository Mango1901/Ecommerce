<?php

namespace Magenest\CustomizePdf\Controller\SoldQty;

use Magenest\CustomizePdf\Api\UpdateSoldQtyValueInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Update implements ActionInterface
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
     * @var UpdateSoldQtyValueInterface
     */
    protected $updateSoldQtyValue;

    /**
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     * @param UpdateSoldQtyValueInterface $updateSoldQtyValue
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory      $resultJsonFactory,
        UpdateSoldQtyValueInterface      $updateSoldQtyValue
    ) {
        $this->_request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->updateSoldQtyValue = $updateSoldQtyValue;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $dataResponse = [
            'success' => false
        ];
        if ($this->_request->isAjax()) {
            $postValue = $this->_request->getPostValue();
            if ($productId = $postValue['productId']) {
                $dataResponse = $this->updateSoldQtyValue->execute($productId);
            }
        }
        return $resultJson->setData($dataResponse);
    }
}
