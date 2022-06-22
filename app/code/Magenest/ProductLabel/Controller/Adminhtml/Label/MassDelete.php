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
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use phpDocumentor\Reflection\Types\This;

/**
 * Class MassDelete
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class MassDelete extends Label
{
    public function execute()
    {
        $collection = $this->filter->getCollection($this->labelRepository->createCollection());
        $labelDeleted = 0;
        foreach ($collection as $item) {
            try {
                $this->labelRepository->delete($item);
                $labelDeleted++;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while delete items.'));
                $this->logger->critical($e);
                return $this->_redirect('*/*/');
            }
        }
        $this->messageManager->addSuccessMessage(__('You have successfully deleted %1 item(s).', $labelDeleted));
        return $this->_redirect('*/*/');
    }
}
