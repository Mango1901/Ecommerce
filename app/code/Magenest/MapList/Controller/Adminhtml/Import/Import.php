<?php
/**
 * Created by PhpStorm.
 * User: hahoang
 * Date: 23/01/2019
 * Time: 11:49
 */

namespace Magenest\MapList\Controller\Adminhtml\Import;

use Magenest\MapList\Controller\Adminhtml\Location;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Import extends Location
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleDirReader;
    protected $resultPageFactory;
    protected $location;
    protected $locationResource;


    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * Import constructor.
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param \Magenest\MapList\Model\LocationFactory $locationFactory
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Module\Dir\Reader $moduleDirReader
     * @param \Magento\Framework\File\Csv $csvProcessor
     */


    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        \Magenest\MapList\Model\ResourceModel\Location $locationResource,
        LoggerInterface $logger,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
        \Magento\Framework\File\Csv $csvProcessor
    )
    {
        $this->moduleDirReader = $moduleDirReader;
        $this->resultPageFactory = $resultPageFactory;
        $this->csvProcessor = $csvProcessor;
        $this->location = $locationFactory;
        $this->locationResource = $locationResource;


        parent::__construct($context, $coreRegistry, $resultPageFactory, $locationFactory, $logger);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getFiles('import_map');
        $type = ["text/csv", "application/vnd.ms-excel"];
        if ($params['size'] === 0) {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage("Can't import blank file!");
        } else {
            if (in_array($params['type'], $type)) {
                $importRawData = $this->csvProcessor
                    ->setDelimiter(',')
                    ->setEnclosure('"')
                    ->getData($params['tmp_name']);
                $model = $this->location->create();
                foreach ($importRawData as $key => $dataRaw) {
                    if (count($importRawData) <= 1) {
                        $this->messageManager->addErrorMessage(__('Please complete required fields!'));
                        return $resultRedirect->setPath('*/*/');
                    }
                    if ($key != 0) {
                        $data = $this->getArrayData($importRawData[0], $dataRaw);

                        if ($this->validateDataImport($data, $model)) {
                            try {
                                $model->setData($data);
                                $this->locationResource->save($model);
                                $this->messageManager->addNoticeMessage("Import store and maplist successful!");
                            } catch (\Exception $e) {
                                $this->messageManager->addErrorMessage(__('Can\'t Import store and maplist  !'));
                                return $this->resultRedirectFactory->create()->setPath('*/*/');
                            }
                        }
//                        } else {
//                            return $this->resultRedirectFactory->create()->setPath('*/*/');
//                        }
                    }
                }
                $resultRedirect->setPath('*/*/');
            } else {
                $resultRedirect->setPath('*/*/');
                $this->messageManager->addErrorMessage("Please select file type csv for import!");
            }
        }
        return $resultRedirect;
    }

    public function getArrayData($arrayIndex, $arrayValue)
    {
        $data = array();
        foreach ($arrayIndex as $key => $index) {
            if ($index == "icon") {
                $index = "small_image";
            }
            $data += array(
                $index => $arrayValue[$key]
            );
        }
        if (isset($data['gallery'])) {
            $data['gallery'] = implode(";", array_unique(explode(";", $data['gallery'])));
        }
        return $data;
    }

    public function validateDataImport($params, $model)
    {
        $valid = true;
        foreach ($params as $key => $data) {
            if ($data == "") {
                $params[$key] = null;
            }
        }
        if (!isset($params['title'], $params['latitude'], $params['longitude'], $params['is_active'], $params['store'], $params['is_use_default_opening_hours'])) {
            $valid = false;
            $this->messageManager->addErrorMessage(__('Please complete required fields!'));
        } else {
            $existNameLocation = $model
                ->getCollection()
                ->addFieldToFilter('title', $params['title']);
            if (count($existNameLocation) > 0) {
                $this->messageManager->addErrorMessage(__('The Title %1 already exist. Please use other Title', $params['title']));
                $valid = false;
            }
            $existAddressLocation = $model
                ->getCollection()
                ->addFieldToFilter('address', $params['address'])
                ->addFieldToFilter('latitude', $params['latitude'])
                ->addFieldToFilter('longitude', $params['longitude']);
            if (count($existAddressLocation) > 0) {
                $this->messageManager->addErrorMessage(__('The Address %1 already exist. Please use other Address', $params['address']));
                $valid = false;
            }
        }
        return $valid;
    }

}
