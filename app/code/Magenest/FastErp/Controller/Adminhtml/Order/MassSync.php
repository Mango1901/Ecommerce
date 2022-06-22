<?php
namespace Magenest\FastErp\Controller\Adminhtml\Order;

use Magenest\FastErp\Model\SyncOrders;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassSync extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    /** @var SyncOrders */
    private $updateOrder;

    /**
     * Class constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param SyncOrders $updateOrder
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context           $context,
        Filter            $filter,
        SyncOrders        $updateOrder,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter);
        $this->updateOrder       = $updateOrder;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        /** @var Order[] $orders */
        $orders = $collection->getItems();
        $count  = $this->updateOrder->execute($orders);
        $this->messageManager->addSuccessMessage(__('%1 order(s) have been synced to ERP.', $count));

        return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
    }
}
