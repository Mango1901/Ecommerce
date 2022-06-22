<?php

namespace Magenest\Promobar\Controller\Adminhtml\Promobars;

class Delete extends \Magenest\Promobar\Controller\Adminhtml\Promobars
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
		if ($id) {
            try {
                $model = $this->_promobarModel->create();
                $model->setId($id);
                $model->load($id);
				$title =  $model->getTitle();
                $widgetInstance = $this->_initWidgetInstance(null,$model->getData('instance_id_widget'));
                $widgetInstance->delete();
                $model->delete();
				$this->messageManager->addSuccess(__('You deleted the item "%1".', $title));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
