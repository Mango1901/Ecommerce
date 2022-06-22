<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 11:09
 */

namespace Magenest\MapList\Controller\Adminhtml\Holiday;

use Magento\Backend\App\Action;
use Magenest\MapList\Controller\Adminhtml\Holiday;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Edit
 * @package Magenest\MapList\Controller\Adminhtml\Holiday
 */
class Edit extends Holiday
{


    /**
     * @var \Magenest\MapList\Model\HolidayFactory
     */
    protected $_holidayLocationFactory;


    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param \Magenest\MapList\Model\LocationFactory $locationFactory
     * @param \Magenest\MapList\Model\HolidayFactory  $holidayFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\HolidayLocationFactory $holidayLocationFactory,
        \Magenest\MapList\Model\HolidayFactory $holidayFactory,
        LoggerInterface $logger
    ) {
        $this->_holidayLocationFactory = $holidayLocationFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $holidayFactory, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->holidayFactory->create();
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This holiday doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }

            try {
                $holidayData = $this->_holidayLocationFactory->create()->getCollection()
                    ->addFieldToFilter('holiday_id', $id)->getData();
            } catch (\Exception $e) {
                $LocationData = array();
            }

            $this->coreRegistry->register('maplist_holiday_selected_location', $holidayData);
        }

        $this->coreRegistry->register('holiday', $model);

        $data = $this->_session->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('maplist_holiday_edit', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()
            ->prepend(
                $model->getId() ? __('Edit Holiday', $model->getData('name')) : __('New Holiday')
            );

        return $resultPage;
    }
}
