<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Mapping;
/**
 * Class Delete
 * @package Magenest\SellOnInstagram\Controller\Adminhtml\Mapping
 */
class Delete extends AbstractMapping
{

    public function execute()
    {
        $redirectResult = $this->resultRedirectFactory->create();
        try {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $mappingModel = $this->mappingFactory->create();
                $this->mappingResource->load($mappingModel, $id);
                if (!$mappingModel->getId()) {
                    throw new \Exception(__(__("Mapping template with Id %1 doesn't exit.", $id)));
                }
                $this->mappingResource->delete($mappingModel);
                $this->messageManager->addSuccessMessage(__("The Mapping template with Id is %1 was deleted.", $id));
            } else {
                $this->messageManager->addSuccessMessage(__("Something when wrong. Please try again."));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->debug($exception->getMessage());
        }
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
