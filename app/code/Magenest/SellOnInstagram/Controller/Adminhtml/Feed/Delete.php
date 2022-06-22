<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

class Delete extends AbstractFeed
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
                $this->instagramFeedResource->delete($instagramFeedModel);
                $this->messageManager->addSuccessMessage(__("Feed with Id is %1 was deleted.", $id));
            } else {
                $this->messageManager->addSuccessMessage(__("Something when wrong. Please try again."));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->debug($exception->getMessage());
        }
        return $this->_redirect("*/*");
    }
}
