<?php
namespace Magenest\FastErp\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class Complete extends Action
{
    public const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param Context $context
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Context                  $context
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id    = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($id);

        try {
            $order->setState(Order::STATE_COMPLETE)->setStatus('complete');
            $this->orderRepository->save($order);
            $this->_eventManager->dispatch(
                "order_management_action_dispatch_save_comment_history",
                [
                    'order'   => $order,
                    'comment' => __("Order have been marked as Complete.")
                ]
            );
            $this->messageManager->addSuccessMessage(__('Order is completed.'));
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception);
        }

        return $this->_redirect('sales/order/view', ['order_id' => $id]);
    }
}
