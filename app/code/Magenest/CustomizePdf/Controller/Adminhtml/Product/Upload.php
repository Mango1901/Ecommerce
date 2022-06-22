<?php
namespace Magenest\CustomizePdf\Controller\Adminhtml\Product;

use Magenest\CustomizePdf\Model\Attachment\File\Uploader as FileUploader;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 * @package Magenest\CustomizePdf\Controller\Adminhtml\Product
 */
class Upload extends \Magento\Backend\App\Action
{

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * Upload constructor.
     * @param FileUploader $fileUploader
     * @param Action\Context $context
     */
    public function __construct(
        FileUploader $fileUploader,
        Action\Context $context
    ) {
        $this->fileUploader = $fileUploader;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $fileId = $this->getRequest()->getParam('param_name');
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $result = $this->fileUploader
                ->setAllowedExtensions(['pdf'])
                ->saveToTmpFolder($fileId);
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }
        return $resultJson->setData($result);
    }
}
