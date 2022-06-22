<?php

namespace Magenest\PaymentEPay\Controller\Webview;

use Magenest\PaymentEPay\Block\Webview\OrderEpayForm;
use Magenest\PaymentEPay\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\Page;
use Magento\Sales\Model\OrderRepository;

/**
 * Class OrderEpay
 * @package Magenest\PaymentEPay\Controller\Webview
 */
class OrderEpay extends Action implements CsrfAwareActionInterface
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Data
     */
    protected $data;
    /**
     * @var OrderEpayForm
     */
    protected $epayForm;
    /**
     * @var Json
     */
    private $json;

    /**
     * OrderEpay constructor.
     * @param Context $context
     * @param OrderRepository $orderRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $data
     * @param OrderEpayForm $epayForm
     * @param Json $json
     */
    public function __construct(
        Context $context,
        OrderRepository $orderRepository,
        ScopeConfigInterface $scopeConfig,
        Data $data,
        OrderEpayForm $epayForm,
        Json $json
    ) {
        $this->json = $json;
        $this->epayForm = $epayForm;
        $this->data = $data;
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
