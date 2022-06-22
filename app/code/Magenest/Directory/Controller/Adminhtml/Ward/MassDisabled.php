<?php

namespace Magenest\Directory\Controller\Adminhtml\Ward;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Directory\Model\WardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magenest\Directory\Controller\Adminhtml\Ward;
use Magento\Framework\App\Cache\Type\Config;

/**
 * Class Disabled
 * @package Magenest\Directory\Controller\Adminhtml\Ward
 */
class MassDisabled extends Ward
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param WardFactory $wardFactory
     * @param Filter $filter
     * @param Config $config
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        WardFactory $wardFactory,
        Filter $filter,
        Config $config,
        TypeListInterface $cacheTypeList
    ) {
        $this->_filter = $filter;
        parent::__construct($context, $resultPageFactory, $wardFactory, $config, $cacheTypeList);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_getWardCollection());
        $disabled = $this->getRequest()->getParam('disabled');
        $updatedCounter = 0;
        try {
            foreach ($collection->getItems() as $ward) {
                $ward->setDisableOnStorefront($disabled);
                $ward->save();
                $updatedCounter++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedCounter));
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception);
        } finally {
            $this->reInitObject();
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath('*/*/index');
        }
    }
}
