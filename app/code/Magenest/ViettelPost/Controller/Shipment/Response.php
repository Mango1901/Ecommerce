<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 25/11/2019
 * Time: 18:31
 */

namespace Magenest\ViettelPost\Controller\Shipment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as ResourceModel;
use Psr\Log\LoggerInterface;

class Response extends Action implements CsrfAwareActionInterface
{
    protected $scopeConfig;

    protected $orderFactory;

    protected $source;

    private $logger;

    protected $messageList = [
        200 => "Received from Postman-Receiving Post Office",
        201 => "Cancel key in delivery note",
        202 => "Correct delivery note",
        300 => "Close delivery file",
        301 => "Close delivery pack Deliver from",
        302 => "Close delivery mail track Deliver from",
        303 => "Close delivery truck lane Deliver from",
        400 => "Receiving income file Receive at",
        401 => "Receiving pocket bag Receive at",
        402 => "Receiving mail track Receive at",
        403 => "Receiving truck lane Receive at",
        500 => "Deliver to Delivery Postman",
        501 => "Successful-Delivering success",
        502 => "Delivering back to Receiver Post Office",
        503 => "Cancel-Customer's requirement",
        504 => "Successful-Delivering back to Customer",
        505 => "Inventories-Delivering back to Receiver Post Office",
        506 => "Inventories-No pick up Customer",
        507 => "Inventories-Customer pick up at Pos Office",
        508 => "Delivering",
        509 => "Delivering to other Post Office",
        510 => "Cancel delivering",
        515 => "Delivering Post Office return order approval",
        550 => "Request Deliver Post Office re send"
    ];

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LoggerInterface      $logger,
        OrderFactory         $orderFactory,
        ResourceModel        $resource,
        Context              $context
    ) {
        parent::__construct($context);
        $this->orderFactory = $orderFactory;
        $this->source       = $resource;
        $this->logger       = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $data = json_decode(urldecode($this->_request->getContent()), true);
        $this->logger->debug(var_export($data, true));
        if (isset($data['DATA'])) {
            $data = $data['DATA'];
        } else {
            return;
        }
        if (!empty($data) && isset($data['ORDER_REFERENCE']) && isset($data['ORDER_NUMBER'])
            && isset($data['ORDER_STATUS'])
        ) {

            $incrementId = $data['ORDER_REFERENCE'];
            $order       = $this->orderFactory->create();
            $this->source->load($order, $incrementId, 'increment_id');
            if (!$order->getId()) {
                return;
            }
            $order->setApiOrderId($data['ORDER_NUMBER']);
            $order->setShippingStatus($data['ORDER_STATUS']);

            if (isset($this->messageList[$data['ORDER_STATUS']])) {
                $this->_eventManager->dispatch("order_management_action_dispatch_save_comment_history", [
                    'order'   => $order,
                    'comment' => "VIETTELPOST: " . $this->messageList[$data['ORDER_STATUS']]
                ]);
            }

            if ($data['ORDER_STATUS'] == 501) {
                $order->setStatus(Order::STATE_COMPLETE)->setState(Order::STATE_COMPLETE);
            }
            try {
                $this->source->save($order);
            } catch (\Exception $e) {

            }
        }
        return;
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        // TODO: Implement createCsrfValidationException() method.
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
