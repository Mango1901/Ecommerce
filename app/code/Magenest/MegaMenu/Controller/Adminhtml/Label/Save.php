<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MegaMenu\Controller\Adminhtml\Label;

use Magenest\MegaMenu\Model\Label;
use Magenest\MegaMenu\Model\LabelFactory;
use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Magenest\MegaMenu\Controller\Adminhtml\Menu
 */
class Save extends Action
{
    /** @var \Magenest\MegaMenu\Model\Menu */
    protected $_model;

    /** @var \Magenest\MegaMenu\Model\LabelFactory */
    protected $_labelFactory;


    /**
     * Save constructor.
     * @param Action\Context $context
     * @param LabelFactory $labelFactory
     */
    public function __construct(
        Action\Context $context,
        LabelFactory $labelFactory
    ) {
        parent::__construct($context);
        $this->_labelFactory = $labelFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        try {
            $labelModel = $this->_labelFactory->create();
            if ($this->getRequest()->getParam('duplicate')) {
                $id = $this->getRequest()->getParam('id');
                $labelModel->load($id);
                $labelModel->unsetData('label_id');
                $labelModel->setData('name', $labelModel->getName() . ' (copy)');
                $labelModel->save();

                $this->messageManager->addSuccessMessage(__('Label data has been successfully duplicated.'));

                return $this->_redirect('*/*/edit', ['id' => $labelModel->getId()]);
            }
            if (!$data) {
                return $this->_redirect('menu/label/edit');
            }
            $labelModel->setData($data);
            if (isset($data['label_id'])) {
                $labelModel->setLabelId($data['label_id']);
            }
            $labelModel->save();
            $this->messageManager->addSuccess(__('Label data has been successfully saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $this->_redirect('*/*/edit', ['id' => $labelModel->getId(), '_current' => true]);
            }
            if ($this->getRequest()->getParam('duplicate')) {
                return $this->_redirect('*/*/edit', ['id' => $labelModel->getId(), '_current' => true]);
            }

            return $this->_redirect('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return $this->_redirect('*/*/edit', ['id' => $labelModel->getId(), '_current' => true]);
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_MegaMenu::label');
    }
}
