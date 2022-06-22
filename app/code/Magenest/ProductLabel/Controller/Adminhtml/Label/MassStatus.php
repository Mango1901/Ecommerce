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
 * Class MassStatus
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class MassStatus extends Label
{
    public function execute()
    {
        $labelUpdated = 0;
        $collection = $this->filter->getCollection($this->labelRepository->createCollection());
        $status = (int) $this->getRequest()->getParam('status');
        foreach ($collection as $item) {
            try {
                $item->setStatus($status);
                $this->labelRepository->save($item);
                $labelUpdated++;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while update items.'));
                $this->logger->critical($e);
                return $this->_redirect('*/*/');
            }
        }
        $this->messageManager->addSuccessMessage(__('You have successfully updated %1 item(s).', $labelUpdated));
        return $this->_redirect('*/*/');
    }
}
