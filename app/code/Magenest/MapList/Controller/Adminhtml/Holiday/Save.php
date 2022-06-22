<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 17/09/2016
 * Time: 14:46
 */

namespace Magenest\MapList\Controller\Adminhtml\Holiday;

use Magenest\MapList\Controller\Adminhtml\Holiday;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;


class Save extends Holiday
{
    /**
     * @var \Magenest\MapList\Model\HolodayFactory
     */
    protected $_holidayFactory;
    protected $_holidayLocationFactory;
    protected $jsonHelper;

    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\HolidayFactory $holidayFactory,
        LoggerInterface $logger,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magenest\MapList\Model\HolidayLocationFactory $holidayLocationFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->_adapterFactory = $adapterFactory;
        $this->_holidayFactory = $holidayFactory;
        $this->_holidayLocationFactory = $holidayLocationFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $holidayFactory, $logger);
    }
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */

    /**
     * Import action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $storeList = $this->jsonHelper->jsonDecode($params['store_list']);
        $model = $this->_holidayFactory->create();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($params) {
            if (!isset($params['holiday_id'])) {
                $existNameHoliday = $model->getCollection()->addFieldToFilter('holiday_name', $params['holiday_name']);
                if (count($existNameHoliday) > 0) {
                    $this->messageManager->addErrorMessage(__('The Name already exist. Please use other Name'));
                    // redirect to edit form
                    return $resultRedirect->setPath('*/*/new');
                }
                if($params["date"] > $params["holiday_date_to"] && $params["holiday_date_to"] != null){
                    $this->messageManager->addErrorMessage(__('End date must be greater than start date'));
                    // redirect to edit form
                    return $resultRedirect->setPath('*/*/new');
                }
            }
            if (isset($params['holiday_id'])) {
                $model->load($params['holiday_id']);
            }
            try {
                if($params["date"] > $params["holiday_date_to"] && $params["holiday_date_to"] != null){
                    $this->messageManager->addErrorMessage(__('End date must be greater than start date'));
                    // redirect to edit form
                    return $resultRedirect->setPath('*/*/edit', array('id' => $model->getId(), '_current' => true));
                }
                $model->addData($params);
                $model->save();
                try {
                    $this->saveHolidayLocation($model->getId(), $storeList);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }

                $this->messageManager->addSuccessMessage(__('Holiday successfully saved.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', array('id' => $model->getId(), '_current' => true));
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving the methods.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($params);
                return $resultRedirect->setPath('*/*/edit', array('id' => $this->getRequest()->getParam('holiday_id')));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }


    private function saveHolidayLocation($holidayId, $storeList)
    {
        $currentHolidayLocationData = $this->_holidayLocationFactory->create()->getCollection()
            ->addFieldToFilter('holiday_id', $holidayId)->getData();
        $paserdCurrentStoreData = array();
        try {
            foreach ($currentHolidayLocationData as $holidayLocationData) {
                $paserdCurrentStoreData[] = $holidayLocationData['location_id'];
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $removeListId = array_diff($paserdCurrentStoreData, $storeList);
        $addListId = array_diff($storeList, $paserdCurrentStoreData);

        foreach ($addListId as $store) {
            if (!!$store) {
                try {
                    $this->_holidayLocationFactory->create()
                        ->addData(array('holiday_id' => $holidayId, 'location_id' => $store))
                        ->save();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }

        try {
            $holidayLocationWillRemove = $this->_holidayLocationFactory->create()
                ->getCollection()
                ->addFieldToFilter('location_id', $removeListId)
                ->addFieldToFilter('holiday_id', $holidayId)
                ->getData();
            if (!!$holidayLocationWillRemove) {
                foreach ($holidayLocationWillRemove as $value) {
                    try {
                        $this->_holidayLocationFactory->create()
                            ->load($value['holiday_location_id'])
                            ->delete();
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

}
