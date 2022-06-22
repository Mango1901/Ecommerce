<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

class Template extends AbstractFeed
{

    public function execute()
    {
        try {
            $resultRaw = $this->resultRawFactory->create();
            return $resultRaw->setContents(
                $this->layoutFactory->create()->createBlock(
                    \Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab\FeedLog::class,
                    'instagram_feed_edit_tab_log'
                )->toHtml()
            );
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }
    }
}
