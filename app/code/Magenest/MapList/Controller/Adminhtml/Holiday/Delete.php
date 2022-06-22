<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 11:09
 */

namespace Magenest\MapList\Controller\Adminhtml\Holiday;

use Magenest\MapList\Controller\Adminhtml\Holiday;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class Delete extends Holiday
{

    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\HolidayFactory $holidayFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $holidayFactory, $logger);
    }

    public function execute()
    {
        $holiday_id = $this->getRequest()->getParam('id');
        $model = $this->holidayFactory->create()->load($holiday_id);
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $model->delete();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($model->getData());

            return $resultRedirect->setPath('*/*/edit', array('id' => $holiday_id));
        }
        $this->messageManager->addSuccess(
            __('A total of 1 record(s) have been deleted.')
        );
        return $resultRedirect->setPath('*/*/index');
    }
}
