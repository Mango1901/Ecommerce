<?php

namespace Magenest\Directory\Controller\Adminhtml\City;

use Magenest\Directory\Controller\Adminhtml\City;
use Magenest\Directory\Model\CityFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Disabled
 * @package Magenest\Directory\Controller\Adminhtml\City
 */
class MassDisabled extends City
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
     * @param CityFactory $cityFactory
     * @param Filter $filter
     * @param Config $config
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CityFactory $cityFactory,
        Filter $filter,
        Config $config,
        TypeListInterface $cacheTypeList
    )
    {
        $this->_filter = $filter;
        parent::__construct($context, $resultPageFactory, $cityFactory, $config, $cacheTypeList);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_getCityCollection());
        $disabled = $this->getRequest()->getParam('disabled');
        $updateEncounter = 0;
        try {
            foreach ($collection->getItems() as $city) {
                $city->setDisableOnStorefront($disabled);
                $city->save();
                $updateEncounter++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updateEncounter));
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
