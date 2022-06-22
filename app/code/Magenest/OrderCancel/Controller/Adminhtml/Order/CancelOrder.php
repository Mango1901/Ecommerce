<?php
namespace Magenest\OrderCancel\Controller\Adminhtml\Order;

use Magenest\OrderCancel\Model\Order\Source\FrontendCancelReason;
use Magenest\OrderCancel\Model\OrderManagement;
use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Psr\Log\LoggerInterface;

class CancelOrder extends Order
{
    const ADMIN_RESOURCE = 'Magento_Sales::cancel';

    /** @var OrderManagement */
    private $management;

    /** @var FrontendCancelReason */
    private $cancelReason;

    /**
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param InlineInterface $translateInline
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param RawFactory $resultRawFactory
     * @param FrontendCancelReason $cancelReason
     * @param OrderManagement $management
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context           $context,
        Registry                 $coreRegistry,
        FileFactory              $fileFactory,
        InlineInterface          $translateInline,
        PageFactory              $resultPageFactory,
        JsonFactory              $resultJsonFactory,
        LayoutFactory            $resultLayoutFactory,
        RawFactory               $resultRawFactory,
        FrontendCancelReason     $cancelReason,
        OrderManagement          $management,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface          $logger
    ) {
        $this->management = $management;
        $this->cancelReason = $cancelReason;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_initOrder();
        $sendEmail = (bool)$this->getRequest()->getParam('cancel_notification');
        if ($order) {
            try {
                $reason = $this->getCancelReason($this->getRequest());
                $this->management->cancelOrder($order, $reason, $sendEmail);
                $this->messageManager->addSuccessMessage(__('You canceled the order.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not canceled the item.'));
                $this->logger->debug($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }

            return $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
        }

        return $resultRedirect->setPath('sales/*/');
    }

    /**
     * @param RequestInterface $request
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
            $result .= " - " . $params['other_reason'];
        }

        return $result;
    }
}
