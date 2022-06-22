<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Controller\Adminhtml\Label;

use Magenest\ProductLabel\Controller\Adminhtml\Label;

/**
 * Class MassReindex
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class MassReindex extends Label
{
    public function execute()
    {
        $labelUpdated = 0;
        $collection = $this->filter->getCollection($this->labelRepository->createCollection());
        foreach ($collection as $item) {
            try {
                $this->labelIndexer->executeByLabelId($item->getId());
                $labelUpdated++;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while reindex items.'));
                $this->logger->critical($e);
                return $this->_redirect('*/*/');
            }
        }
        $this->messageManager->addSuccessMessage(__('You have successfully reindex %1 item(s).', $labelUpdated));
        return $this->_redirect('*/*/');
    }
}
