<?php
namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;


class Index extends AbstractFeed
{
    public function execute()
    {
        try {
            $resultPage = $this->resultPageFactory->create();
            $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Manage Feeds'));
            return $resultPage;
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
