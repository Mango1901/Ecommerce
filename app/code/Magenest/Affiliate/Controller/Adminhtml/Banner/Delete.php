<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Banner;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magenest\Affiliate\Controller\Adminhtml\Banner;

/**
 * Class Delete
 * @package Magenest\Affiliate\Controller\Adminhtml\Banner
 */
class Delete extends Banner
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \Magenest\Affiliate\Model\Banner $banner */
                $banner = $this->_objectManager->create('Magenest\Affiliate\Model\Banner');
                $banner->load($id)->delete();

                $this->messageManager->addSuccessMessage(__('The Banner has been deleted.'));
                $this->_redirect('affiliate/*/');

                return;
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while deleting banner data. Please review the action log and try again.')
                );
                $this->_redirect('affiliate/*/edit', ['id' => $id]);

                return;
            }
        }

        $this->messageManager->addErrorMessage(__('We cannot find a banner to delete.'));
        $this->_redirect('affiliate/*/');
    }
}
