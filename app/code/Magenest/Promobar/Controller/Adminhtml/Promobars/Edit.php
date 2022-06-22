<?php

namespace Magenest\Promobar\Controller\Adminhtml\Promobars;

class Edit extends \Magenest\Promobar\Controller\Adminhtml\Promobars
{


    /**
     * Edit sitemap
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magenest\Promobar\Model\Promobar');
        $mobileModel = $this->_objectManager->create('Magenest\Promobar\Model\MobilePromobar');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            $mobileModel->load($id,'promobar_id');
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('promobar/*/');
                return;
            }
            if($model->getData('multiple_content') == "\"\""){
                $this->messageManager->addError(__('Something wrong when load data.'));
                $this->_redirect('promobar/*/');
                return;
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
            $mobileModel->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('promobar_promobars', $model);
        $this->_coreRegistry->register('promobar_mobile_promobars', $mobileModel);

        // 5. Build edit form
        $this->_initAction()->_addBreadcrumb(
            $id ? __('Edit %1', $model->getTitle()) : __('New Item'),
            $id ? __('Edit %1', $model->getTitle()) : __('New Item')
        )->_addContent(
            $this->_view->getLayout()->createBlock('Magenest\Promobar\Block\Adminhtml\Promobars\Edit')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Promobars'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('New Item')
        );
        $this->_view->renderLayout();
    }
}
