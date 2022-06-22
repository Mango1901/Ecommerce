<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

use Magenest\SellOnInstagram\Model\InstagramFeed;

class Edit extends AbstractFeed
{

    public function execute()
    {
        try {
            $saleRuleModel = $this->ruleFactory->create();
            $instagramFeed = $this->initCurrentFeed();
            if ($instagramFeed->getId()) {
                $saleRuleModel->setData('conditions_serialized', $instagramFeed->getConditionsSerialized());
            }
            $this->coreRegistry->register(InstagramFeed::REGISTER_SALE_RULE, $saleRuleModel);
            $resultPage = $this->resultPageFactory->create();
            $title = $instagramFeed->getId() ? $instagramFeed->getTitle() : __('New Instagram Shopping');
            $resultPage->setActiveMenu(AbstractFeed::ADMIN_RESOURCE)
                ->addBreadcrumb($title, $title)
                ->addBreadcrumb($title, $title);
            return $resultPage;
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }
    }
}
