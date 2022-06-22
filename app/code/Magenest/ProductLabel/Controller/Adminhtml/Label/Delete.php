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
 * Class Delete
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class Delete extends Label
{
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('label_id');
        if ($id) {
            try {
                $model = $this->labelRepository->get($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('Delete product label successfully!'));

                return $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t delete item right now. Please review the log and try again.'));
                $this->logger->critical($e);
                $this->_redirect('*/*/');

                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a item to delete.'));
        return $this->_redirect('*/*/');
    }
}
