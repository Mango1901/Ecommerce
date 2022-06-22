<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\City;

use Magenest\Directory\Controller\Adminhtml\City;
use Magenest\Directory\Model\CityFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Directory\Helper\DirectoryHelper;
/**
 * Class Delete
 * @package Magenest\Directory\Controller\Adminhtml\City
 */
class Delete extends City
{
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    public function __construct
    (
        Context $context,
        PageFactory $resultPageFactory,
        CityFactory $cityFactory,
        Config $configCacheType,
        DirectoryHelper $directoryHelper,
        TypeListInterface $cacheTypeList
    )
    {
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context, $resultPageFactory, $cityFactory, $configCacheType, $cacheTypeList);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $city = $this->_initObject();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($city) {
            try {
            if ($this->directoryHelper->validateCityBeforeDelete($city->getId()) == false) {
                $this->messageManager->addErrorMessage(__('The city can not be deleted.'));
            } else {
                $city->delete();
                $this->reInitObject();
                $this->messageManager->addSuccessMessage(__('The city is deleted.'));
            }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
