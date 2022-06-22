<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magento\Sales\Controller\Adminhtml\Order as Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magenest\OrderManagement\Model\Order;
use Psr\Log\LoggerInterface;
use Magenest\OrderManagement\Helper\Authorization as AuthorizationHelper;

/**
 * Class AbstractAction
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
abstract class AbstractAction extends Action
{
    /**
     * @var Order
     */
    protected $_omOrder;

    /**
     * @var Session
     */
    protected $_adminSession;

    /**
     * @var AuthorizationHelper
     */
    protected $_authorizationHelper;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param InlineInterface $translateInline
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param RawFactory $resultRawFactory
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param Order $omOrder
     * @param Session $adminSession
     * @param AuthorizationHelper $authorizationHelper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        InlineInterface $translateInline,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $resultLayoutFactory,
        RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        Order $omOrder,
        Session $adminSession,
        AuthorizationHelper $authorizationHelper
    ) {
        $this->_omOrder = $omOrder;
        $this->_adminSession = $adminSession;
        $this->_authorizationHelper = $authorizationHelper;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
    }
}