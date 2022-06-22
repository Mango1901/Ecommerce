<?php
namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;
use Magenest\SellOnInstagram\Model\InstagramFeed;

class Log extends AbstractFeed
{
    public function execute()
    {
        try {
            $feedModel = $this->getFeedModel();
            $this->coreRegistry->register(InstagramFeed::REGISTER, $feedModel);
            return $this->resultLayoutFactory->create();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
