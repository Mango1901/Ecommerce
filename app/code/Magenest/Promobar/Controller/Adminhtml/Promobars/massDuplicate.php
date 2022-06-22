<?php

namespace Magenest\Promobar\Controller\Adminhtml\Promobars;

use Magento\Backend\App\Action;

class massDuplicate extends \Magenest\Promobar\Controller\Adminhtml\Promobars
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
                $modelMobileDuplicate =  $this->_mobilePromobarModel->create();
                $modelDuplicate =  $this->_promobarModel->create();
                foreach ($ids as $id) {
                    $model =  $this->_promobarModel->create();
                    $mobileModel =  $this->_mobilePromobarModel->create();
                    $data = $model->load($id)->getData();
                    $data['title'] = $this->checkTitleBeforeDuplicate($model->getData('title'));
                    $data['status'] = "1";
                    $data['instance_id_widget'] = "";
                    unset($data['promobar_id']);
                    $modelDuplicate->setData($data)->save();

                    $mobileData = $mobileModel->load($id)->getData();
                    $mobileData['promobar_id'] = $modelDuplicate->getId();
                    unset($mobileData['mobile_promobar_id']);
                    $modelMobileDuplicate->setData($mobileData)->save();
                    $id = $modelDuplicate->getId();
                    $modelMobileDuplicate->setData('promobar_id', $id)->save();
//                    $instance_id = $this->createWidget($data,$id);
//                    $modelDuplicate->setData('instance_id_widget',$instance_id)->save();
                }
                $this->messageManager->addSuccess(__('Total of %1 record(s) were successfully duplicate.', count($ids)));

            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
