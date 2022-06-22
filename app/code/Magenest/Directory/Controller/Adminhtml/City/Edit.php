<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\City;

use Magenest\Directory\Controller\Adminhtml\City;
use Magenest\Directory\Model\CityFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magenest\Directory\Block\Adminhtml\City\Edit as EditBlock;
use Magento\Framework\App\Cache\Type\Config;

/**
 * Class Edit
 * @package Magenest\Directory\Controller\Adminhtml\City
 */
class Edit extends City
{
	/**
	 * @var Registry
	 */
	protected $_registry;
    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CityFactory $cityFactory
     * @param Config $config
     * @param Registry $registry
     */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		CityFactory $cityFactory,
        Config $config,
        TypeListInterface $cacheTypeList,
		Registry $registry
	)
	{
		$this->_registry = $registry;
		parent::__construct($context, $resultPageFactory, $cityFactory, $config, $cacheTypeList);
    }

	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$city = $this->_initObject();
		if (!$city) {
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('*');

			return $resultRedirect;
		}
		//Set entered data if was error when we do save
		$data = $this->_session->getData('city_form', true);
		if (!empty($data)) {
			$city->addData($data);
		}

		$this->_registry->register('current_city', $city);
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_initAction();
		$resultPage->getConfig()->getTitle()->prepend($city->getId() ? __('Edit City \'%1\'', $city->getName()) : __('Create New City'));
		$resultPage->getLayout()->addBlock(EditBlock::class, 'city', 'content');

		return $resultPage;
	}
}
