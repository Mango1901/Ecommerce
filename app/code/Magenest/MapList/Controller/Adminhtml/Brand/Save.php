<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 17/09/2016
 * Time: 14:46
 */

namespace Magenest\MapList\Controller\Adminhtml\Brand;

use Magenest\MapList\Controller\Adminhtml\Brand;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magenest\MapList\Model\Theme\Upload;


class Save extends Brand
{
    /**
     * @var \Magenest\MapList\Model\BrandFactory
     */
    protected $_brandFactory;
    protected $jsonHelper;
    /**
     * @var
     */
    protected $_uploadImage;

    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\BrandFactory $brandFactory,
        LoggerInterface $logger,
        Upload $uploadImage,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->_adapterFactory = $adapterFactory;
        $this->_brandFactory = $brandFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_uploadImage = $uploadImage;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $brandFactory, $logger);
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
        $logo = $this->getRequest()->getFiles('logo');
        $model = $this->_brandFactory->create();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($params) {
            if (!isset($params['brand_id'])) {
                $existNameHoliday = $model->getCollection()->addFieldToFilter('name', $params['name']);
                if (count($existNameHoliday) > 0) {
                    $this->messageManager->addErrorMessage(__('The Name already exist. Please use other Name'));
                    // redirect to edit form
                    return $resultRedirect->setPath('*/*/new');
                }
            }
            if (isset($params['brand_id'])) {
                $model->load($params['brand_id']);
            }
            try {
                if (isset($logo) && $logo['name'] != '') {
                    try {
                        $uploader = $this->getUploadImage();
                        $params['logo'] = $uploader->uploadFileAndGetName('logo', $uploader->getBaseImageDir(), $params);
                        if ($params['logo'] == 'error') {
                            $params['logo'] = '';
                            $this->messageManager->addErrorMessage("Your photos couldn't be uploaded. Photos should be saved as JPG, PNG, GIF, JPEG ");
                        }
                    } catch (LocalizedException $e) {
                        throw new LocalizedException(__('Wrong Upload Logo.'));
                    }
                } else {
                    if (!empty($params['logo']['delete']) && !empty($params['logo']['value'])) {
                        $uploader = $this->getUploadImage();
                        $uploader->deleteFile($params['logo']['value']);
                        $params['logo'] = "";
                    } elseif (isset($params['brand_id'])) {
                        $params['logo'] = $model->getData('logo');
                    } else {
                        $params['logo'] = "";
                    }
                }
                $model->addData($params);
                $model->save();
                $this->messageManager->addSuccessMessage(__('Brand successfully saved.'));
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
                return $resultRedirect->setPath('*/*/edit', array('id' => $this->getRequest()->getParam('brand_id')));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
    public function getUploadImage()
    {
        return $this->_uploadImage;
    }
}
