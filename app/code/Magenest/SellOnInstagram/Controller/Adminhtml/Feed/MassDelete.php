<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

class MassDelete extends AbstractFeed
{

    public function execute()
    {
        try {
            $count = 0;
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            foreach ($collection->getItems() as $item) {
                $this->instagramFeedResource->delete($item);
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were deleted.', $count));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->debug($e->getMessage());
        }
        return $this->_redirect('*/*');
    }
}
