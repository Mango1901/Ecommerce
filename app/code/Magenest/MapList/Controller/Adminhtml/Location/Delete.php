<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 11:09
 */

namespace Magenest\MapList\Controller\Adminhtml\Location;

use Magenest\MapList\Controller\Adminhtml\Location;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class Delete extends Location
{

    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $locationFactory, $logger);
    }

    public function execute()
    {
        $location_id = $this->getRequest()->getParam('id');
        $model = $this->locationFactory->create()->load($location_id);
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $model->delete();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($model->getData());

            return $resultRedirect->setPath('*/*/edit', array('id' => $location_id));
        }
        $this->messageManager->addSuccess(
            __('A total of 1 record(s) have been deleted.')
        );
        return $resultRedirect->setPath('*/*/index');
    }
}
