<?php
namespace Magenest\MapList\Controller\Adminhtml\Import;

use Magenest\MapList\Controller\Adminhtml\Import  as ImportController;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends ImportController implements ActionInterface
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->messageManager->addNoticeMessage(
            $this->_objectManager->get(\Magento\ImportExport\Helper\Data::class)->getMaxUploadSizeMessage()
        );
        $this->messageManager->addNoticeMessage(
            'Please upload image into folder "/pub/media/catalog/category" before import!'
        );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_MapList::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));
        $resultPage->addBreadcrumb(__('Import'), __('Import'));
        return $resultPage;
    }
}
