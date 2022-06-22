<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Group;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magenest\Affiliate\Controller\Adminhtml\Group;
use RuntimeException;

/**
 * Class Save
 * @package Magenest\Affiliate\Controller\Adminhtml\Group
 */
class Save extends Group
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('group')) {

            $group = $this->_initGroup();
            $group->setData($data);

            $this->_eventManager->dispatch('affiliate_group_prepare_save', ['group' => $group, 'action' => $this]);
            try {
                $group->save();
                $this->messageManager->addSuccess(__('The Group has been created successfully.'));
                $this->_getSession()->setData('affiliate_group_data', false);

                $resultRedirect->setPath('affiliate/*/');

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Group.'));
            }
            $this->_getSession()->setData('affiliate_group_data', $data);

            $resultRedirect->setPath('affiliate/*/create', ['_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }
}
