<?php
namespace Magenest\MapList\Controller\Adminhtml\Export;

use Magento\Framework\App\ActionInterface;
use Magenest\MapList\Controller\Adminhtml\Export as ExportController;
use Magento\Framework\Controller\ResultFactory;

class Index extends ExportController implements ActionInterface
{
    /**
     * Index action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_MapList::export');
        $resultPage->getConfig()->getTitle()->prepend(__('Export'));
        $resultPage->getConfig()->getTitle()->prepend(__('Export'));
        $resultPage->addBreadcrumb(__('Export'), __('Export'));
        return $resultPage;
    }
}
