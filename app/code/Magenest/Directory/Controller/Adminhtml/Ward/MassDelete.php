<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\Ward;

use Magenest\Directory\Helper\DirectoryHelper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Directory\Model\WardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magenest\Directory\Controller\Adminhtml\Ward;
use Magento\Framework\App\Cache\Type\Config;

/**
 * Class MassDelete
 * @package Magenest\Directory\Controller\Adminhtml\Ward
 */
class MassDelete extends Ward
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
     * @param WardFactory $wardFactory
     * @param Filter $filter
     * @param Config $config
     * @param DirectoryHelper $directoryHelper
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        WardFactory $wardFactory,
        Filter $filter,
        Config $config,
        DirectoryHelper $directoryHelper,
        TypeListInterface $cacheTypeList
    ) {
        $this->_filter = $filter;
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context, $resultPageFactory, $wardFactory, $config, $cacheTypeList);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_getWardCollection());
        $deletedCounter = 0;
        $arrIds = [];
        foreach ($collection->getItems() as $ward) {
            if ($this->directoryHelper->validateWardBeforeDelete($ward->getId()) == false) {
                array_push($arrIds,$ward->getId());
                continue;
            }
            $ward->delete();
            $deletedCounter++;
        }
        $this->reInitObject();
        if  (!empty($arrIds)) {
            $strIds = implode(',',$arrIds);
            $this->messageManager->addErrorMessage(__('Can not delete these wards %1'),$strIds);
        } else {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $deletedCounter));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/index');
    }
}
