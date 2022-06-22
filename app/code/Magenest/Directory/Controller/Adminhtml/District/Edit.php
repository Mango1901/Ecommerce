<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\District;

use Magenest\Directory\Controller\Adminhtml\District;
use Magenest\Directory\Model\DistrictFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magenest\Directory\Block\Adminhtml\District\Edit as EditBlock;
use Magento\Framework\App\Cache\Type\Config;

/**
 * Class Edit
 * @package Magenest\Directory\Controller\Adminhtml\District
 */
class Edit extends District
{
	/**
	 * @var Registry
	 */
	protected $_registry;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param DistrictFactory $districtFactory
     * @param Registry $registry
     * @param Config $config
     * @param TypeListInterface $cacheTypeList
     */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		DistrictFactory $districtFactory,
		Registry $registry,
        Config $config,
        TypeListInterface $cacheTypeList
	)
	{
		$this->_registry = $registry;
		parent::__construct($context, $resultPageFactory, $districtFactory, $config, $cacheTypeList);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$district = $this->_initObject();
		if (!$district) {
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('*');

			return $resultRedirect;
		}
		//Set entered data if was error when we do save
		$data = $this->_session->getData('district_form', true);
		if (!empty($data)) {
			$district->addData($data);
		}

		$this->_registry->register('current_district', $district);
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_initAction();
		$resultPage->getConfig()->getTitle()->prepend($district->getId() ? __('Edit District \'%1\'', $district->getName()) : __('Create New District'));
		$resultPage->getLayout()->addBlock(EditBlock::class, 'district', 'content');

		return $resultPage;
	}
}
