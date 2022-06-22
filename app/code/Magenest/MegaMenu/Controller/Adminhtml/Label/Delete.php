<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MegaMenu\Controller\Adminhtml\Label;

use Magenest\MegaMenu\Model\Label;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 * @package Magenest\MegaMenu\Controller\Adminhtml\Menu
 */
class Delete extends Action
{
    protected $_labelModel;

    public function __construct(
        Action\Context $context,
        Label $_labelModel
    ) {
        parent::__construct($context);
        $this->_labelModel = $_labelModel;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id && (int) $id > 0) {
            try {
                $this->_labelModel->load($id);

                if ($this->_labelModel->getLabelId()) {
                    $this->_labelModel->delete();
                    $this->messageManager->addSuccessMessage(__('Label has been deleted.'));
                    return $resultRedirect->setPath('*/*/');
                } else {
                    throw new LocalizedException(__("Label to delete was not found."));
                }
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/', ['id' => $id]);
            }
        }

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
