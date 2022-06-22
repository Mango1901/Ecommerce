<?php
namespace Magenest\FastErp\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassComplete extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    /** @var LoggerInterface */
    private $logger;

    /**
     * Class constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context           $context,
        Filter            $filter,
        LoggerInterface   $logger,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter);
        $this->logger            = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $count = 0;

        /** @var Order $order */
        foreach ($collection->getItems() as $order) {
            try {
                $order->setState(Order::STATE_COMPLETE)->setStatus('complete');
                $order->save();
                $count++;
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }

        $this->messageManager->addSuccessMessage(__('%1 order(s) have been marked as "Complete" status.', $count));

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
}
