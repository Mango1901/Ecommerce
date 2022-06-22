<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Banner;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magenest\Affiliate\Controller\Adminhtml\Banner;

/**
 * Class Save
 * @package Magenest\Affiliate\Controller\Adminhtml\Banner
 */
class Save extends Banner
{
    /**
     * @return $this|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPost('banner');
        if (!$data) {
            return $resultRedirect->setPath('affiliate/*/');
        }

        /** @var \Magenest\Affiliate\Model\Banner $banner */
        $banner = $this->_initBanner();
        $bannerId = $this->getRequest()->getParam('id');

        if (!$banner->getId() && $bannerId) {
            $this->messageManager->addErrorMessage(__('This banner does not exist.'));

            return $resultRedirect->setPath('affiliate/*/');
        }

        if (!empty($data)) {
            $banner->addData($data);
            $this->_getSession()->setData('affiliate_banner_data', $data);
        }

        try {
            $banner->save();
            $this->_getSession()->setData('affiliate_banner_data', false);

            $this->messageManager->addSuccessMessage(__('You saved the banner.'));
        } catch (LocalizedException $e) {
            $redirectBack = true;
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $redirectBack = true;
            $this->messageManager->addErrorMessage(__('We cannot save the banner.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return ($redirectBack)
            ? $resultRedirect->setPath('affiliate/*/edit', ['id' => $banner->getId()])
            : $resultRedirect->setPath('affiliate/*/');
    }
}
