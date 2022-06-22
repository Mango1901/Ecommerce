<?php

namespace Magenest\CustomFrontend\Plugin\Controller\Cart;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Add
 *
 * Used for plugin navigator to checkout index page
 */
class Add
{
    /**
     * @param RequestInterface
     */
    private $request;

    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Add constructor.
     * @param RequestInterface $request
     * @param Json $jsonSerializer
     * @param UrlInterface $url
     * @param ManagerInterface $manager
     * @param Cart $cart
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        Json $jsonSerializer,
        UrlInterface $url,
        ManagerInterface $manager,
        Cart $cart,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->jsonSerializer = $jsonSerializer;
        $this->_url = $url;
        $this->messageManager = $manager;
        $this->cart = $cart;
        $this->logger = $logger;
    }

    /**
     * Function afterExecute
     *
     * @param \Magento\Checkout\Controller\Cart\Add $subject
     * @param $result
     * @return ResponseInterface|ResultInterface
     */
    public function afterExecute(\Magento\Checkout\Controller\Cart\Add $subject, $result)
    {
        try {
            if ($this->request->getParam('buy_now') &&
                $this->cart->getQuote() &&
                !$this->cart->getQuote()->getHasError()
            ) {
                $content['backUrl'] = $this->_url->getUrl('checkout/index/index');
                $subject->getResponse()->representJson($this->jsonSerializer->serialize($content));
                $this->messageManager->getMessages(true);
                return $subject->getResponse();
            } elseif ($this->request->getParam('installment_payment') &&
                $this->cart->getQuote() &&
                !$this->cart->getQuote()->getHasError()
            ) {
                $content['backUrl'] = $this->_url->getUrl('epay/customer/installmentpayment');
                $subject->getResponse()->representJson($this->jsonSerializer->serialize($content));
                $this->messageManager->getMessages(true);
                return $subject->getResponse();
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }
}
