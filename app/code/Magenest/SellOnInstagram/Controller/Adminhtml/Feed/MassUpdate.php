<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

class MassUpdate extends AbstractFeed
{
    public function execute()
    {
        try {
            $count = 0;
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $status = $this->getRequest()->getParam('status');
            foreach ($collection->getItems() as $item) {
                $item->setStatus($status);
                $this->instagramFeedResource->save($item);
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were modified.', $count));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->debug($exception->getMessage());
        }
        return $this->_redirect('*/*');
    }
}
