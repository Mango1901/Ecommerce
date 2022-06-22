<?php

namespace Magenest\SalesPerson\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class AssignedToSales extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magenest_SalesPerson::add_queue';

    const TOPIC_NAME = 'salesperson.queue.topic';

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    protected $publisher;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;
    /**
     * @var \Magenest\SalesPerson\Helper\AssignedToSales
     */
    protected $assignedtosales;

    protected $request;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magenest\SalesPerson\Helper\AssignedToSales $assignedToSales
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magenest\SalesPerson\Helper\AssignedToSales $assignedToSales,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        Action\Context $context
    ) {
        $this->resultFactory = $resultFactory;
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->assignedtosales = $assignedToSales;
        $this->publisher = $publisher;
        $this->json = $json;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $params = $this->request->getParams();
            if (isset($params["selected"])) {
                foreach ($params["selected"] as $orderId) {
                    $order = $this->orderRepository->get($orderId);
                    $orderIncrementId = $order->getIncrementId();
                    $data = [
                        "user_id" => $params["user_id"],
                        "order_id" => $orderIncrementId
                    ];
                    $this->assignedtosales->assignQueueData($data);
                }
                $this->messageManager->addSuccessMessage(__('The order had been assigned.'));
                return $resultRedirect->setPath('sales/order/index');
            } else {
                $order = $this->getOrder();
                $orderIncrementId = $order->getIncrementId();
                $data = [
                    "user_id" =>  $params["assigned_to_person"],
                    "order_id" => $orderIncrementId
                ];
                $this->messageManager->addSuccessMessage(__('The order had been assigned.'));
                $this->assignedtosales->assignQueueData($data);
                return $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
            }
        } catch (\Exception $exception) {
        }
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
}
