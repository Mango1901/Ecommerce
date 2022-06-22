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

use Magento\Backend\App\Action;
use Magenest\ProductLabel\Controller\Adminhtml\Label;

/**
 * Class Reindex
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class Reindex extends Label
{
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('label_id');
        if ($id) {
            try {
                $this->labelIndexer->executeByLabelId($id);
                $this->messageManager->addSuccessMessage(__('You have reindexed the label.'));
                $this->_redirect('*/*/edit', ['label_id' =>  $id]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t reindex label right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('*/*/edit', ['label_id' =>  $id]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a item to reindex.'));
        $this->_redirect('*/*/');
    }
}
