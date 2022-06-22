<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Mapping;

/**
 * Class Index
 * @package Magenest\SellOnInstagram\Controller\Adminhtml\Mapping
 */
class Index extends AbstractMapping
{

    public function execute()
    {
        try {
            $resultPage = $this->resultPageFactory->create();
            $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Mapping Attributes'));
            return $resultPage;
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
