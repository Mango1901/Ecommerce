<?php
namespace Magenest\Popup\Controller\Adminhtml\Log;

use Magento\Ui\Component\MassAction;
use Psr\Log\LoggerInterface;

class MassDelete extends \Magento\Backend\App\Action {
    protected $_filer;
    protected $_popupLogCollectionFactory;
    protected $_logger;
    public function __construct(
        \Magenest\Popup\Model\ResourceModel\Log\CollectionFactory $popupLogCollectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_popupLogCollectionFactory = $popupLogCollectionFactory;
        $this->_filer = $filter;
        $this->_logger = $logger;
        parent::__construct($context);
    }
    public function execute()
    {
        try{
            $collection = $this->_filer->getCollection($this->_popupLogCollectionFactory->create());
            $count = 0;
            foreach ($collection->getItems() as $item){
                $item->delete();
                $count++;
            }
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been deleted.', $count)
            );
        }catch (\Exception $exception){
            $this->messageManager->addError($exception->getMessage());
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}