<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\Ward;

use Magenest\Directory\Controller\Adminhtml\Ward;
use Magenest\Directory\Helper\DirectoryHelper;
use Magenest\Directory\Model\WardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Delete
 * @package Magenest\Directory\Controller\Adminhtml\Ward
 */
class Delete extends Ward
{
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        WardFactory $wardFactory,
        Config $configCacheType,
        DirectoryHelper $directoryHelper,
        TypeListInterface $cacheTypeList
    )
    {
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context, $resultPageFactory, $wardFactory, $configCacheType, $cacheTypeList);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $ward = $this->_initObject();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($ward) {
            try {
                if  ($this->directoryHelper->validateWardBeforeDelete($ward->getId()) == false) {
                    $this->messageManager->addErrorMessage('The ward can not be deleted.');
                } else {
                    $ward->delete();
                    $this->reInitObject();
                    $this->messageManager->addSuccessMessage(__('The ward is deleted.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
