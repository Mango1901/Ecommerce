<?php
/**
 * @copyright Copyright (c) magenest.com, Inc. (https://www.magenest.com)
 */

namespace Magenest\Report\Controller\Adminhtml\Export;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\Report\Model\Export\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

/**
 * Class Render
 */
class GridToCsv extends Action
{
    const LIMIT_EXPORT_ITEMS = 500;
    /**
     * @var ConvertToCsv
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context $context
     * @param ConvertToCsv $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context      $context,
        ConvertToCsv $converter,
        FileFactory  $fileFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export data provider to XML
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected', false);
        $error = false;

        if ($selected == "false") {
            $this->messageManager->addErrorMessage(__('Please choose the item to export.'));
            $error = true;
        } elseif (is_array($selected) && count($selected) > self::LIMIT_EXPORT_ITEMS) {
            $error = true;
            $this->messageManager->addErrorMessage(__('Exceeding the permitted limits.'));
        }

        if ($error) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('report/order/index');

            return $resultRedirect;
        }

        return $this->fileFactory->create('export.csv', $this->converter->getCsvFile(), 'var');
    }
}