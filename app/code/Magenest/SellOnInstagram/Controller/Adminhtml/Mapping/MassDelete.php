<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Mapping;

/**
 * Class MassDelete
 * @package Magenest\SellOnInstagram\Controller\Adminhtml\Mapping
 */
class MassDelete extends AbstractMapping
{

    public function execute()
    {
        try {
            $count = 0;
            $collections = $this->filter->getCollection($this->mappingCollectionFactory->create());
            foreach ($collections->getItems() as $item) {
                $this->mappingResource->delete($item);
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were deleted.', $count));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
