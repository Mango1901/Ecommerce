<?php

namespace Magenest\Directory\Controller\Adminhtml\District;

use Magenest\Directory\Controller\Adminhtml\District;
use Magenest\Directory\Model\DistrictFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Disabled
 * @package Magenest\Directory\Controller\Adminhtml\District
 */
class MassDisabled extends District
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
     * @param DistrictFactory $districtFactory
     * @param Filter $filter
     * @param Config $config
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        DistrictFactory $districtFactory,
        Filter $filter,
        Config $config,
        TypeListInterface $cacheTypeList
    )
    {
        $this->_filter = $filter;
        parent::__construct($context, $resultPageFactory, $districtFactory, $config, $cacheTypeList);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_getDistrictCollection());
        $disabled = $this->getRequest()->getParam('disabled');
        $updatedCounter = 0;
        try {
            foreach ($collection->getItems() as $district) {
                $district->setDisableOnStorefront($disabled);
                $district->save();
                $updatedCounter++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been udpated.', $updatedCounter));
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
