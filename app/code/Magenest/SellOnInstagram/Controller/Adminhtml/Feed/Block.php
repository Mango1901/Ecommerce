<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

class Block extends AbstractFeed
{

    public function execute()
    {
        try {
            $resultRaw = $this->resultRawFactory->create();
            return $resultRaw->setContents(
                $this->layoutFactory->create()->createBlock(
                    \Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab\FeedLogHistory::class,
                    'instagram_feed_history_tab'
                )->toHtml()
            );
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }
    }
}
