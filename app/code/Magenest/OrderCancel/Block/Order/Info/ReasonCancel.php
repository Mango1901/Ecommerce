<?php

namespace Magenest\OrderCancel\Block\Order\Info;

use Magento\Framework\App\Http\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Spi\OrderStatusHistoryResourceInterface;

class ReasonCancel extends Template
{
    const PREFIX_REASON_ENGLISH = 'Order have been cancelled. Reason:';
    const PREFIX_REASON_VIETNAMESE = 'Đơn hàng đã bị hủy. Lý do:';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param Template\Context $context
     * @param Registry $registry
     * @param Context $httpContext
     * @param ResourceConnection $resourceConnection
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry         $registry,
        Context          $httpContext,
        ResourceConnection $resourceConnection,
        array            $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Function used for check order is cancel
     * @return bool
     */
    public function getReasonCancel()
    {
        try {
            $order = $this->getOrder();

            if ($order && $order->getStatus() === 'canceled') {


                return $this->getCommentReasonData($order->getId());
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return false;
    }

    public function getCommentReasonData($orderId)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $select = $connection->select()
                ->from(
                    $connection->getTableName('sales_order_status_history'),
                    ['comment']
                )->where(
                    'parent_id = ?',
                    $orderId
                )->where(
                    'status = ?',
                    'canceled'
                )->where(
                    implode(
                        ' OR ',
                        [
                            $connection->quoteInto('comment LIKE ?', '%'. self::PREFIX_REASON_ENGLISH .'%'),
                            $connection->quoteInto('comment LIKE ?', '%'. self::PREFIX_REASON_VIETNAMESE .'%')
                        ]
                    )
                );

            $result = $connection->fetchOne($select);
            if ($result) {
                return str_replace([self::PREFIX_REASON_ENGLISH, self::PREFIX_REASON_VIETNAMESE], '', $result);
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return null;
    }
}
