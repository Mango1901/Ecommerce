<?php
namespace Magenest\FastErp\Controller\Adminhtml\Order;

use Magenest\FastErp\Model\UpdateOrder;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class Confirm extends Action
{
    public const ADMIN_RESOURCE = 'Magenest_FastErp::order_confirm';

    protected $orderRepository;

    protected $updateOrder;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        UpdateOrder              $updateOrder,
        Context                  $context
    ) {
        $this->orderRepository = $orderRepository;
        $this->updateOrder     = $updateOrder;
        parent::__construct($context);
    }

    public function execute()
    {
        $id    = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($id);

        try {
            $order->setState(Order::STATE_PROCESSING)->setStatus('confirmed');
            $this->orderRepository->save($order);
            $this->_eventManager->dispatch(
                "order_management_action_dispatch_save_comment_history",
                [
                    'order'   => $order,
                    'comment' => __("Order have been confirmed.")
                ]
            );
            $this->messageManager->addSuccessMessage(__('Order has been confirmed successfully'));
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception);
        }

        return $this->_redirect('sales/order/view', ['order_id' => $id]);
    }
}
