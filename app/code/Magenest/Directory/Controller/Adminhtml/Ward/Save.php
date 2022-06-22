<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\Ward;

use Magenest\Directory\Controller\Adminhtml\Ward;

/**
 * Class Save
 * @package Magenest\Directory\Controller\Adminhtml\Ward
 */
class Save extends Ward
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $ward = $this->_initObject();
            if (!empty($data)) {
                $ward->addData($data);
            }

            try {
                $ward->save();
                $this->reInitObject();
                $this->messageManager->addSuccessMessage(__('The ward saved successfully.'));
                $this->_getSession()->unsData('ward_form');
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit', ['id' => $ward->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setData('ward_form', $data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

            return;
        }

        $this->_redirect('*/*/');
    }
}
