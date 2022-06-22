<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MegaMenu\Controller\Adminhtml\Menu;

use Magenest\MegaMenu\Model\ResourceModel\MegaMenu;
use Magenest\MegaMenu\Model\ResourceModel\MenuEntity;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 * @package Magenest\MegaMenu\Controller\Adminhtml\Menu
 */
class Delete extends Action
{

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id && (int)$id > 0) {
            try {
                /** @var \Magenest\MegaMenu\Model\MegaMenu $model */
                $model = $this->_objectManager->create('Magenest\MegaMenu\Model\MegaMenu');
                $model->load($id);

                if ($model->getId()) {
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('MegaMenu has been deleted.'));

                    return $resultRedirect->setPath('*/*/');
                } else {
                    throw new LocalizedException(__("MegaMenu to delete was not found."));
                }
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());

                // go back to edit form
                return $resultRedirect->setPath('*/*/', ['id' => $id]);
            }
        }

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
