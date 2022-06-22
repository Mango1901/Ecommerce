<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml;

use Magenest\Directory\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Directory\Model\DistrictFactory;
use Magento\Backend\App\Action;
use Magenest\Directory\Model\District as DistrictModel;
use Magento\Framework\App\Cache\Type\Config;

/**
 * Class District
 * @package Magenest\Directory\Controller\Adminhtml
 */
abstract class District extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magenest_Directory::district';

    /**
     * @type PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var DistrictFactory
     */
    protected $_districtFactory;
    /**
     * @var Config
     */
    protected $configCacheType;
    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param DistrictFactory $districtFactory
     * @param Config $configCacheType
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        DistrictFactory $districtFactory,
        Config $configCacheType,
        TypeListInterface $cacheTypeList
    ) {

        $this->_resultPageFactory = $resultPageFactory;
        $this->_districtFactory = $districtFactory;
        $this->configCacheType = $configCacheType;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Directory::district');
        $resultPage->addBreadcrumb(__('District'), __('District'));
        $resultPage->addBreadcrumb(__('District'), __('District'));

        return $resultPage;
    }

    /**
     * Init District
     *
     * @return bool|DistrictModel
     */
    protected function _initObject()
    {
        $districtId = (int)$this->getRequest()->getParam('id');
        $district = $this->_districtFactory->create();

        if ($districtId) {
            $district->load($districtId);
            if (!$district->getId()) {
                $this->messageManager->addErrorMessage(__('This district no longer exists.'));

                return false;
            }
        }

        return $district;
    }

    /**
     * Get district collection
     *
     * @return mixed
     */
    protected function _getDistrictCollection()
    {
        return $this->_districtFactory->create()->getCollection();
    }

    protected function reInitObject(){
        $this->configCacheType->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG,[Data::CACHE_TAG_DISTRICT, Data::CACHE_TAG_DATA]);
        $this->cacheTypeList->invalidate('config');
    }
}
