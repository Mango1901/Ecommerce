<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 17/09/2016
 * Time: 14:46
 */

namespace Magenest\MapList\Controller\Adminhtml\Location;

use Magenest\MapList\Controller\Adminhtml\Location;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\Uploader;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;


class Save extends Location
{
    /**
     * @var \Magenest\MapList\Model\LocationFactory
     */
    protected $_locationFactory;
    protected $_fileUploaderFactory;
    protected $_adapterFactory;
    protected $_filesystem;
    protected $locationProductFactory;
    protected $locationGalleryCollection;
    protected $imageUploader;
    protected $jsonHelper;
    /**
     * @var WriteInterface
     */
    protected $_mediaDirectory;


    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magenest\MapList\Model\LocationProductFactory $locationProductFactory,
        \Magenest\MapList\Model\ResourceModel\LocationGallery\CollectionFactory $locationCollectionFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->_adapterFactory = $adapterFactory;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_locationFactory = $locationFactory;
        $this->locationProductFactory = $locationProductFactory;
        $this->locationGalleryCollection = $locationCollectionFactory;
        $this->imageUploader = $imageUploader;
        $this->jsonHelper = $jsonHelper;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context, $coreRegistry, $resultPageFactory, $locationFactory, $logger);
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
        $productList = $this->jsonHelper->jsonDecode($params['product_list']);
        $model = $this->_locationFactory->create();
        $resultRedirect = $this->resultRedirectFactory->create();
        $collectionGallery = $this->locationGalleryCollection->create();
        if ($params) {
            if (!isset($params['location_id'])) {
                $existNameLocation = $this->_locationFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('title', $params['title']);
                if (count($existNameLocation) > 0) {
//                    return $resultRedirect->setPath('*/*/new');
                    $this->messageManager->addErrorMessage(__('The Title already exist. Please use other Title'));
                    // redirect to edit form
                    return $resultRedirect->setPath('*/*/new');
                }

                $existAddressLocation = $this->_locationFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('address', $params['address'])
                    ->addFieldToFilter('latitude', $params['latitude'])
                    ->addFieldToFilter('longitude', $params['longitude']);
                if (count($existAddressLocation) > 0) {
//                    return $resultRedirect->setPath('*/*/new');
                    $this->messageManager->addErrorMessage(__('The Address already exist. Please use other Address'));
                    // redirect to edit form
                    $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($params);
                    return $resultRedirect->setPath('*/*/edit', array('id' => $this->getRequest()->getParam('location_id')));
                }
            } else {
                $existAddressLocation = $this->_locationFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('address', $params['address'])
                    ->addFieldToFilter('latitude', $params['latitude'])
                    ->addFieldToFilter('longitude', $params['longitude']);
                if (count($existAddressLocation) > 1) {
//                    return $resultRedirect->setPath('*/*/new');
                    $this->messageManager->addErrorMessage(__('The Address already exist. Please use other Address'));
                    // redirect to edit form
                    $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($params);
                    return $resultRedirect->setPath('*/*/edit', array('id' => $this->getRequest()->getParam('location_id')));
                }
            }

            if (isset($params['location_id'])) {
                $model->load($params['location_id']);
            }

            try {
                $i = 0;
                $dataGallery = array();
                foreach ($collectionGallery as $locationGallery) {
                    if ($locationGallery != null) {
                        if ($locationGallery->getData('type_image') == 2) {
                            $dataGallery[$i] = $locationGallery->getData('name_image');
                            $locationGallery->delete();
                            $i++;
                        }

                        if ($locationGallery->getData('type_image') == 1) {
                            $dataIcon = $locationGallery->getData('name_image');
                            $locationGallery->delete();
                        }
                    }
                }

                if (isset($dataGallery[0])) {
                    foreach ($dataGallery as $data) {
                        try {
                            $baseTmpPath = $this->imageUploader->getBaseTmpPath();
                            $basePath = $this->imageUploader->getBasePath();
                            if(!$this->_mediaDirectory->isExist($basePath."/".$data)){
                                if($this->_mediaDirectory->isExist($baseTmpPath."/".$data)){
                                    $this->imageUploader->moveFileFromTmp($data);
                                }else{
                                    $this->messageManager->addErrorMessage("Can't upload image name: ".$data);
                                }
                            }
                        } catch (\Exception $e) {
                            $this->messageManager->addErrorMessage($e->getMessage());
                        };
                    }

                    $gallerys = implode(';', $dataGallery);
                    if ($model->getGallery() != null) {
                        $preImage = $model->getData('gallery');
                        $curImage = $preImage . ';' . $gallerys;
                        $model->setData('gallery', $curImage);
                    } else {
                        $model->setData('gallery', $gallerys);
                    }
                }

                if (isset($dataIcon)) {
                    $icon = $dataIcon;
                    $model->setData('small_image', $icon);
                    try {
                        $baseTmpPath = $this->imageUploader->getBaseTmpPath();
                        $basePath = $this->imageUploader->getBasePath();
                        if(!$this->_mediaDirectory->isExist($basePath."/".$icon)){
                            if($this->_mediaDirectory->isExist($baseTmpPath."/".$icon)){
                                $this->imageUploader->moveFileFromTmp($icon);
                            }else{
                                $this->messageManager->addErrorMessage("Can't upload image name: ".$icon);
                            }
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    };
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            $payment = isset($params['payment_methods']) ? $this->jsonHelper->jsonEncode($params['payment_methods']) : '';
            $params['payment_methods'] = $payment;
            $brands = isset($params['brands']) != '' ? $this->jsonHelper->jsonEncode($params['brands']) : '';
            $params['brands'] = $brands;
            try {
                $model->addData($params);
                $opening_hours = '';

                $is_use_default_opening_hours = 0;
                if (isset($params['is_use_default_opening_hours'])) {
                    $is_use_default_opening_hours = 1;
                }else{
                    $opening_hours = $this->jsonHelper->jsonEncode($params["opening_hours"]);
                }
//                $model->setOpeningHours($opening_hours);
//                $model->setIsUseDefaultOpeningHours($is_use_default_opening_hours);
                $templateSpecialDate = '';
                if (isset($params['special_date'])) {
                    $templateSpecialDate = $this->jsonHelper->jsonEncode($params['special_date']);
                }

                $model->setSpecialDate($templateSpecialDate);


                $store = implode(",", $model['store']);
                $model['store'] = $store;
                $model->save();

                try {
                    $this->saveProductLocation($model->getId(), $productList);
                    $this->_eventManager->dispatch(
                        'add_store_to_product_after_save_location',
                        array('id' => $model->getId(), 'product_list' => $productList)
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }

                $this->messageManager->addSuccessMessage(__('Location successfully saved.'));
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

                return $resultRedirect->setPath('*/*/edit', array('id' => $this->getRequest()->getParam('location_id')));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }


    private function saveProductLocation($locationId, $productList)
    {
        $currentLocationProductData = $this->locationProductFactory->create()->getCollection()
            ->addFieldToFilter('location_id', $locationId)->getData();
        $paserdCurrentProductData = array();
        try {
            foreach ($currentLocationProductData as $locationProductData) {
                $paserdCurrentProductData[] = $locationProductData['product_id'];
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $removeListId = array_diff($paserdCurrentProductData, $productList);
        $addListId = array_diff($productList, $paserdCurrentProductData);

        foreach ($addListId as $product) {
            if (!!$product) {
                try {
                    $this->locationProductFactory->create()
                        ->addData(array('location_id' => $locationId, 'product_id' => $product))
                        ->save();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }

        try {
            $locationProductWillRemove = $this->locationProductFactory->create()
                ->getCollection()
                ->addFieldToFilter('product_id', $removeListId)
                ->getData();
            if (!!$locationProductWillRemove) {
                foreach ($locationProductWillRemove as $value) {
                    try {
                        $this->locationProductFactory->create()
                            ->load($value['location_product_id'])
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
