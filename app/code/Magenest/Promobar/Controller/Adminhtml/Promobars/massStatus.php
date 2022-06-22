<?php

namespace Magenest\Promobar\Controller\Adminhtml\Promobars;

use Magento\Backend\App\Action;

class MassStatus extends \Magenest\Promobar\Controller\Adminhtml\Promobars
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
        if (is_string($ids)) {
            $ids = explode(",", $ids);
        }
		if(!is_array($ids)) {
            if($ids != ""){
                try {
//                    foreach ($ids as $id) {
                        $model = $this->_objectManager->create('Magenest\Promobar\Model\Promobar')
                            ->load($ids)
                            ->setStatus($this->getRequest()->getPost('status'))
                            ->save();
//                    }
                    $this->messageManager->addSuccess(__('Total of %1 record(s) were successfully updated.', count($ids)));

                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }else{
                            $this->messageManager->addError(__('Please select item(s).'));
            }
        }else {
            try {
                foreach ($ids as $id) {
					$model = $this->_objectManager->create('Magenest\Promobar\Model\Promobar')
						->load($id)
						->setStatus($this->getRequest()->getPost('status'))
						->save();
                }
				$this->messageManager->addSuccess(__('Total of %1 record(s) were successfully updated.', count($ids)));
                
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
