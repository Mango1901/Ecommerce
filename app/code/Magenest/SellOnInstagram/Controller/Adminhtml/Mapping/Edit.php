<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Mapping;

/**
 * Class Edit
 * @package Magenest\SellOnInstagram\Controller\Adminhtml\Mapping
 */
class Edit extends AbstractMapping
{

    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $mappingModel = $this->mappingFactory->create();
            if ($id) {
                $this->mappingResource->load($mappingModel, $id);
                if (!$mappingModel->getId()) {
                    $this->messageManager->addErrorMessage(__('This Mapping template no longer exists.'));
                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/index');
                }
            }
            $this->registerTemplate($mappingModel);
            $title = $mappingModel->getId() ? $mappingModel->getName() : __('New Mapping Template');
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu(AbstractMapping::ADMIN_RESOURCE);
            $resultPage->addBreadcrumb(__('Sell On Instagram'), __('Sell On Instagram'));
            $resultPage->getConfig()->getTitle()->prepend($title);
            return $resultPage;
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }
    }
}
