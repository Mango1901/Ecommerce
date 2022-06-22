<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

use Magenest\SellOnInstagram\Model\InstagramFeed;

class Duplicate extends AbstractFeed
{
    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $instagramFeedModel = $this->instagramFeedFactory->create();
                $this->instagramFeedResource->load($instagramFeedModel, $id);
                if (!$instagramFeedModel->getId()) {
                    throw new \Exception(__("Feed with Id %1 doesn't exit.", $id));
                }
                $instagramFeedModel->unsetData('id');
                $instagramFeedModel->setStatus(InstagramFeed::STATUS_DISABLE);
                $feedName = $instagramFeedModel->getName();
                $instagramFeedModel->setName($feedName . " Duplicate");
                $instagramFeedModel->setStatus(InstagramFeed::STATUS_DISABLE);
                $this->instagramFeedResource->save($instagramFeedModel);
                $this->messageManager->addSuccessMessage(__("Feed data has been successfully duplicated."));
                return $this->_redirect('*/*/edit', ['id' => $instagramFeedModel->getId()]);
            } else {
                $this->messageManager->addSuccessMessage(__("Something when wrong. Please try again."));
                return $this->_redirect('*/*');
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->debug($exception->getMessage());
            return $this->_redirect('*/*');
        }
    }
}
