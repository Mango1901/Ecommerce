<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\City;

use Magenest\Directory\Helper\DirectoryHelper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Directory\Model\CityFactory;
use Magento\Framework\Controller\ResultFactory;
use Magenest\Directory\Controller\Adminhtml\City;
use Magento\Framework\App\Cache\Type\Config;

/**
 * Class MassDelete
 * @package Magenest\Directory\Controller\Adminhtml\City
 */
class MassDelete extends City
{
    /**
     * @var Filter
     */
    protected $_filter;
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CityFactory $cityFactory
     * @param Filter $filter
     * @param Config $config
     * @param DirectoryHelper $directoryHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CityFactory $cityFactory,
        Filter $filter,
        Config $config,
        DirectoryHelper $directoryHelper,
        TypeListInterface $cacheTypeList
    ) {
        $this->_filter = $filter;
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context, $resultPageFactory, $cityFactory, $config, $cacheTypeList);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_getCityCollection());
        $deletedCounter = 0;
        $arrIds = [];
        foreach ($collection->getItems() as $city) {
            if ($this->directoryHelper->validateCityBeforeDelete($city->getId()) == false) {
                array_push($arrIds,$city->getId());
                continue;
            }
            $city->delete();
            $deletedCounter++;
        }
        $this->reInitObject();
        if (!empty($arrIds)) {
            $strIds = implode(',',$arrIds);
            $this->messageManager->addErrorMessage(__('Can not delete these cities %1', $strIds));
        } else {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $deletedCounter));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/index');
    }
}
