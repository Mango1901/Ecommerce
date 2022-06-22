<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Label
 * @package Magenest\ProductLabel\Controller\Adminhtml
 */
abstract class Label extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magenest\ProductLabel\Api\LabelRepositoryInterface
     */
    protected $labelRepository;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magenest\ProductLabel\Model\Indexer\LabelIndexer
     */
    protected $labelIndexer;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\Cache\TypeList
     */
    protected $typeList;

    /**
     * Label constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PageFactory $pageFactory
     * @param \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magenest\ProductLabel\Model\Indexer\LabelIndexer $labelIndexer
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\App\Cache\TypeList $typeList
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\ProductLabel\Model\Indexer\LabelIndexer $labelIndexer,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\App\Cache\TypeList $typeList

    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context);
        $this->resultPageFactory = $pageFactory;
        $this->labelRepository = $labelRepository;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
        $this->labelIndexer = $labelIndexer;
        $this->filter = $filter;
        $this->session = $session;
        $this->typeList = $typeList;
    }

    /**
     * @return $this
     */
    protected function _initAction() {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magenest_ProductLabel::manage'
        )->_addBreadcrumb(
            __('Product Labels'),
            __('Product Labels')
        );
        return $this;
    }

    protected function invalidateCache()
    {
        $this->typeList->invalidate(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
        $this->typeList->invalidate(\Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER);
    }
}
