<?php
namespace Magenest\OrderCancel\Controller\Order;

use Magenest\OrderCancel\Model\Order\Source\FrontendCancelReason;
use Magenest\OrderCancel\Model\OrderManagement;
use Magenest\OrderCancel\ViewModel\FrontendCancel;
use Magento\Backend\Model\UrlInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class CancelOrder extends Action
{
    /** @var NotifierInterface */
    private $notifier;

    /** @var OrderManagement */
    private $management;

    /** @var FrontendCancelReason */
    private $cancelReason;

    /** @var LoggerInterface */
    private $logger;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var Session */
    private $customerSession;

    /** @var UrlInterface */
    private $backendUrlBuilder;

    /** @var FrontendCancel */
    private $frontendCancel;

    /**
     * @param Context $context
     * @param FrontendCancelReason $cancelReason
     * @param FrontendCancel $frontendCancel
     * @param OrderManagement $management
     * @param LoggerInterface $logger
     * @param Session $customerSession
     * @param NotifierInterface $notifier
     * @param UrlInterface $backendUrlBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context                  $context,
        FrontendCancelReason     $cancelReason,
        FrontendCancel           $frontendCancel,
        OrderManagement          $management,
        LoggerInterface          $logger,
        Session                  $customerSession,
        NotifierInterface        $notifier,
        UrlInterface             $backendUrlBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->logger            = $logger;
        $this->notifier          = $notifier;
        $this->management        = $management;
        $this->cancelReason      = $cancelReason;
        $this->frontendCancel    = $frontendCancel;
        $this->orderRepository   = $orderRepository;
        $this->customerSession   = $customerSession;
        $this->backendUrlBuilder = $backendUrlBuilder;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \Magento\Sales\Model\Order $order */
        $order     = $this->getOrder();
        $sendEmail = (bool)$this->getRequest()->getParam('cancel_notification');
        if ($order) {
            try {
                if (!$this->frontendCancel->isCancelable($order)
                    || $order->getCustomerId() !== $this->customerSession->getCustomerId()) {
                    throw new LocalizedException(__("The requested order can't be canceled."));
                }
                $reason = $this->getCancelReason($this->getRequest());
                $order->addCommentToStatusHistory(
                    "Order has been canceled by customer '{$this->customerSession->getCustomer()->getName()}'. Reason: {$reason}",
                    false,
                    false
                )->setIsCustomerNotified(false);
                $this->management->cancelOrder($order, $reason, $sendEmail);
                $this->messageManager->addSuccessMessage(__('You canceled the order.'));

                $this->notifier->addNotice(
                    __('Order Cancellation Notice'),
                    __('Order ID %1 has been canceled by customer. Reason: %2', $order->getId(), $reason),
                    $this->backendUrlBuilder->getUrl('sales/order/view', ['order_id' => $order->getId()])
                );

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->debug($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }

        return $resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * @return false|OrderInterface
     */
    private function getOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        try {
            return $this->orderRepository->get($id);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            return false;
        }
    }

    /**
     * @param RequestInterface $request
     *
     * @return string
     */
    private function getCancelReason(RequestInterface $request)
    {
        $result = '';
        $params = $request->getParams();

        if (!empty($params['cancel_reason'])) {
            $result .= $this->cancelReason->getOptionText($params['cancel_reason']);
        }
        if (isset($params['other_reason']) && !empty($params['other_reason'])) {
            $result .= " " . $params['other_reason'];
        }

        return $result;
    }
}
