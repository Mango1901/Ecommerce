<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\District;

use Magenest\Directory\Controller\Adminhtml\District;
use Magenest\Directory\Helper\DirectoryHelper;
use Magenest\Directory\Model\DistrictFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Delete
 * @package Magenest\Directory\Controller\Adminhtml\District
 */
class Delete extends District
{
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        DistrictFactory $districtFactory,
        Config $configCacheType,
        DirectoryHelper $directoryHelper,
        TypeListInterface $cacheTypeList
    )
    {
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context, $resultPageFactory, $districtFactory, $configCacheType, $cacheTypeList);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $district = $this->_initObject();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($district) {
            try {
                if ($this->directoryHelper->validateDistrictBeforeDelete($district->getId()) == false) {
                    $this->messageManager->addErrorMessage(__('The district can not be deleted.'));
                } else {
                    $district->delete();
                    $this->reInitObject();
                    $this->messageManager->addSuccessMessage(__('The district is deleted.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
