<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\City;

use Magenest\Directory\Controller\Adminhtml\City;

/**
 * Class Save
 * @package Magenest\Directory\Controller\Adminhtml\City
 */
class Save extends City
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $city = $this->_initObject();
            if (!empty($data)) {
                $city->addData($data);
            }

            try {
                $city->save();
                $this->reInitObject();
                $this->messageManager->addSuccessMessage(__('The city saved successfully.'));
                $this->_getSession()->unsData('city_form');
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit', ['id' => $city->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setData('city_form', $data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

            return;
        }

        $this->_redirect('*/*/');
    }
}
