<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MapList\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\MapList\Controller\Adminhtml\Import as ImportController;

/**
 * Download sample file controller
 */
class Download extends ImportController
{
    const SAMPLE_FILES_MODULE = 'Magenest_MapList';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $readFactory;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\ImportExport\Model\Import\SampleFileProvider
     */
    private $sampleFileProvider;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\ImportExport\Model\Import\SampleFileProvider $sampleFileProvider
     * @param ComponentRegistrar $componentRegistrar
     * @param \Magenest\MapList\Model\Import\SampleFileProvider|null $sampleFileProvider
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        \Magenest\MapList\Model\Import\SampleFileProvider $sampleFileProvider = null
    ) {
        parent::__construct(
            $context
        );
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->readFactory = $readFactory;
        $this->componentRegistrar = $componentRegistrar;
        $this->sampleFileProvider = $sampleFileProvider
            ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magenest\MapList\Model\Import\SampleFileProvider::class);
    }

    /**
     * Download sample file action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $entityName = $this->getRequest()->getParam('filename');

        if (preg_match('/^\w+$/', $entityName) == 0) {
            $this->messageManager->addErrorMessage(__('Incorrect entity name.'));

            return $this->getResultRedirect();
        }

        try {
            $fileContents = $this->sampleFileProvider->getFileContents($entityName);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('There is no sample file for this entity.'));

            return $this->getResultRedirect();
        }

        $fileSize = $this->sampleFileProvider->getSize($entityName);
        $fileName = $entityName . '.csv';

        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($fileContents);
        return $resultRaw;
    }

    private function getResultRedirect()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/import/index');

        return $resultRedirect;
    }
}
