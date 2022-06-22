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
 * Class ReindexAll
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class ReindexAll extends Label
{
    public function execute()
    {
        try {
            $this->labelIndexer->executeFull();
            $this->messageManager->addSuccessMessage(__('Reindex all labels successfully!'));

            return $this->_redirect('*/*/');
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Something went wrong when reindex all labels'));

            return $this->_redirect('*/*/');
        }
    }
}
