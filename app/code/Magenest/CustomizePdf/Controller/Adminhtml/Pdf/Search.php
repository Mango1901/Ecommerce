<?php
namespace Magenest\CustomizePdf\Controller\Adminhtml\Pdf;

use Magenest\CustomizePdf\Helper\Constant;
use Magenest\CustomizePdf\Model\Filesystem\Driver\File as FileDriver;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class Search
 * @package Magenest\CustomizePdf\Controller\Adminhtml\Pdf
 */
class Search extends \Magento\Backend\App\Action
{
    /** @var FileDriver  */
    protected $_searchFile;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var DirectoryList  */
    protected $directoryList;

    /**
     * Search constructor.
     * @param FileDriver $searchFile
     * @param LoggerInterface $logger
     * @param DirectoryList $directoryList
     * @param Context $context
     */
    public function __construct(
        FileDriver $searchFile,
        LoggerInterface $logger,
        DirectoryList $directoryList,
        Context $context
    ) {
        $this->_searchFile = $searchFile;
        $this->_logger = $logger;
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $search = $this->getRequest()->getParam('query');
            $result = $this->getOptionsTree($search);
            $resultPage->setData($result);
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $resultPage;
    }

    /**
     * @param $search
     * @return array|array[]
     */
    protected function getOptionsTree($search)
    {
        $results = [];
        try {
            $path = $this->directoryList->getPath('media') . "/" . Constant::DATASHEET_PATH;
            $results =  $this->_searchFile->getFileNameDirectory($path, $search);
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $results;
    }
}
