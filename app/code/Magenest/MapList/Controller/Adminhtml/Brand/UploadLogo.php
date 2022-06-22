<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MapList\Controller\Adminhtml\Brand;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;

/**
 * Class UploadLogo
 * @package Magenest\MapList\Controller\Adminhtml\Brand
 */
class UploadLogo extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    protected $_uploader;

    protected $_brand;


    /**
     * UploadLogo constructor.
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magenest\MapList\Model\Theme\Upload $uploader,
        \Magenest\MapList\Model\Brand $brand,
        Action\Context $context
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_filesystem = $filesystem;
        $this->_brand = $brand;
        $this->_uploader = $uploader;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->getRequest()->getParam('id')) {
            $id = $this->getRequest()->getParam('id');
            $brand = $this->_brand->load($id);
            if ($brand->getLogo()) {
                $this->_uploader->deleteFile($brand->getLogo());
            }

            $brand->setLogo('');
            $brand->save();
            return;
        }
    }
}
