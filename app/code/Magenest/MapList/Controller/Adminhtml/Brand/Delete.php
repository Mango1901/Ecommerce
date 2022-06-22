<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 11:09
 */

namespace Magenest\MapList\Controller\Adminhtml\Brand;

use Magenest\MapList\Controller\Adminhtml\Brand;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class Delete extends Brand
{

    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\BrandFactory $brandFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $brandFactory, $logger);
    }

    public function execute()
    {
        $brand_id = $this->getRequest()->getParam('id');
        $model = $this->brandFactory->create()->load($brand_id);
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $model->delete();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($model->getData());

            return $resultRedirect->setPath('*/*/edit', array('id' => $brand_id));
        }
        $this->messageManager->addSuccess(
            __('A total of 1 record(s) have been deleted.')
        );
        return $resultRedirect->setPath('*/*/index');
    }
}
