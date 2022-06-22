<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Controller\Adminhtml\Ward;

use Magenest\Directory\Controller\Adminhtml\Ward;
use Magenest\Directory\Model\WardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magenest\Directory\Block\Adminhtml\Ward\Edit as EditBlock;
use Magento\Framework\App\Cache\Type\Config;

/**
 * Class Edit
 * @package Magenest\Directory\Controller\Adminhtml\Ward
 */
class Edit extends Ward
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
     * @param WardFactory $wardFactory
     * @param Registry $registry
     * @param Config $config
     * @param TypeListInterface $cacheTypeList
     */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		WardFactory $wardFactory,
		Registry $registry,
        Config $config,
        TypeListInterface $cacheTypeList
	)
	{
		$this->_registry = $registry;
		parent::__construct($context, $resultPageFactory, $wardFactory, $config, $cacheTypeList);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$ward = $this->_initObject();
		if (!$ward) {
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('*');

			return $resultRedirect;
		}
		//Set entered data if was error when we do save
		$data = $this->_session->getData('ward_form', true);
		if (!empty($data)) {
			$ward->addData($data);
		}

		$this->_registry->register('current_ward', $ward);
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_initAction();
		$resultPage->getConfig()->getTitle()->prepend($ward->getId() ? __('Edit Ward \'%1\'', $ward->getName()) : __('Create New Ward'));
		$resultPage->getLayout()->addBlock(EditBlock::class, 'ward', 'content');

		return $resultPage;
	}
}
