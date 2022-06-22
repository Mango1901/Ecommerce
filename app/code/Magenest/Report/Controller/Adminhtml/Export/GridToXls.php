<?php
/**
 * @copyright Copyright (c) magenest.com, Inc. (https://www.magenest.com)
 */

namespace Magenest\Report\Controller\Adminhtml\Export;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\Report\Model\Export\ConvertToXls;
use Magento\Framework\App\Response\Http\FileFactory;

/**
 * Class Render
 */
class GridToXls extends Action
{
    /**
     * @var ConvertToXls
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context $context
     * @param ConvertToXml $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        ConvertToXls $converter,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export data provider to XML
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');

        if ($selected == "false") {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('Please choose the item to export.'));
            $resultRedirect->setPath('report/order/index');

            return $resultRedirect;
        }

        return $this->fileFactory->create('export.xls', $this->converter->getXlsFile(), 'var');
    }
}