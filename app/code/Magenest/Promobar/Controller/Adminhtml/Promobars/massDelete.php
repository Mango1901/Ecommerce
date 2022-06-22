<?php

namespace Magenest\Promobar\Controller\Adminhtml\Promobars;

use Magento\Backend\App\Action;

class massDelete extends \Magenest\Promobar\Controller\Adminhtml\Promobars
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
        $ids = $this->getRequest()->getPost('ids');
		if(!is_array($ids)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                foreach ($ids as $id) {
					$model = $this->_objectManager->create('Magenest\Promobar\Model\Promobar')
						->load($id);
                    $widgetInstance = $this->_initWidgetInstance(null,$model->getData('instance_id_widget'));
                    $widgetInstance->delete();
                    $model->delete();
                }
				$this->messageManager->addSuccess(__('Total of %1 record(s) were successfully deleted.', count($ids)));
                
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
