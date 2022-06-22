<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\District;

use Magenest\Directory\Controller\Adminhtml\District;

/**
 * Class Save
 * @package Magenest\Directory\Controller\Adminhtml\District
 */
class Save extends District
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $district = $this->_initObject();
            if (!empty($data)) {
                $district->addData($data);
            }

            try {
                $district->save();
                $this->reInitObject();
                $this->messageManager->addSuccessMessage(__('The district saved successfully.'));
                $this->_getSession()->unsData('district_form');
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit', ['id' => $district->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setData('district_form', $data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

            return;
        }

        $this->_redirect('*/*/');
    }
}
