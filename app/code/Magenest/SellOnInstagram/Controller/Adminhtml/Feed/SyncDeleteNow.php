<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

use Magenest\SellOnInstagram\Model\ProductBatch;

class SyncDeleteNow extends AbstractFeed
{
    const PAGE_SIZE = 1500;

    public function execute()
    {
        try {
            $feed = $this->initCurrentFeed();
            $feed->generateProduct();
            $feed->syncByFeedId();
            $status  = $feed->getStatus();
            if ($status) {
                $feedId = $feed->getId();
                $productIds = $feed->getProductIds();
                $storeId = $feed->getStoreId();
                if (!empty($productIds)) {
                    $this->addToHistoryDetail($productIds, $feedId);
                    $this->processSync($productIds, $feedId, $storeId);
                    $this->messageManager->addSuccessMessage(__("Please check status of sync product in View History"));
                } else {
                    $this->messageManager->addErrorMessage(__("Not found products with your condition, Please check your condition"));
                }
            } else {
                $this->messageManager->addErrorMessage(__("Not found product with your condition, Please check your condition"));
            }
        } catch (\Exception $exception) {
            $this->logger->error("Error Sync Product " . $exception->getMessage());
        }
        if ($this->getRequest()->getParam('edit_page') == true) {
            return $this->_redirect("*/*/edit", ['id' => $feed->getId()]);
        }
        return $this->_redirect("*/*");
    }
    protected function addToHistoryDetail($productIds, $feedId)
    {
        $productBatch = $this->productBatch->setCountItemsSuccess($productIds)->setAction(ProductBatch::DELETE);
        $history = $this->historyModel->updateReport($productBatch, $feedId);
        $history->setLastId($history->getId());
    }

    protected function processSync($productIds, $feedId, $storeId)
    {
        $pageSize = self::PAGE_SIZE;
        $requests = [];
        $productCollection = $this->productCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter('entity_id', ['in' => $productIds]);
        $productCollection->setStoreId($storeId);
        $productCollectionSize = $productCollection->getSize();
        $pages = ceil($productCollectionSize / $pageSize);
        $productCollection->setPageSize($pageSize);
        for ($i = 1; $i <= $pages; $i++) {
            $requests = [];
            $productCollection->setCurPage($i);
            $productCollection->load();
            foreach ($productCollection as $collection) {
                $requests [] = $this->batchBuilder->requestDeleteProductAction($collection);
            }
            $data = $this->batchBuilder->createProductItemTemplate($this->helper->getAccessToken(), $requests);
            $this->productBatch->syncProductToFbShop($data, $feedId);
            $productCollection->clear();
        }
    }
}
