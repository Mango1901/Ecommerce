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
 * Class Edit
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class Edit extends Label
{
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('label_id');
        $model = $this->labelRepository->createNewObject();

        // 2. Initial checking
        if ($id) {
            $model = $this->labelRepository->get($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->coreRegistry->register('magenest_product_label', $model);
        $this->dataPersistor->set('magenest_product_label', $model->getData());

        // 5. Build edit form
        $this->_initAction()->_addBreadcrumb(
            $id ? __('Edit %1', $model->getName()) : __('New Label'),
            $id ? __('Edit %1', $model->getName()) : __('New Label')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Edit Label'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Label')
        );
        $this->_view->renderLayout();
    }
}
