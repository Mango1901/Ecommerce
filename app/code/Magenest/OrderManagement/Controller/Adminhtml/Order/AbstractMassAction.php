<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magenest\OrderManagement\Model\Order as OmOrder;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magenest\OrderManagement\Helper\Authorization as AuthorizationHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Model\Auth\Session;

/**
 * Class AbstractMassAction
 * @package Magenest\OrderManagement\Controller\Adminhtml\Order
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = "Magenest_OrderManagement::general";

    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var OmOrder
     */
    protected $_omOrder;

    /**
     * @var AuthorizationHelper
     */
    protected $_authorizationHelper;

    /**
     * @var Session
     */
    protected $_adminSession;

    /**
     * @var array
     */
    protected $_allowStatuses = [
        'Magenest_OrderManagement::warehouse_complete_shipment' => 'complete',
        'Magenest_OrderManagement::accounting_confirm' => OmOrder::CONFIRMED_WAREHOUSE_SALES_CODE,
        'Magenest_OrderManagement::warehouse_received_goods' => OmOrder::SUPPLIER_CONFIRMED_CODE,
        'Magenest_OrderManagement::accounting_confirm_reimbursed' => OmOrder::NEED_CONFIRM_REIMBURSEMENT_CODE,
        'Magenest_OrderManagement::warehouse_received_returned_goods' => OmOrder::NEED_WAREHOUSE_CONFIRM_CODE,
        'Magenest_OrderManagement::supplier_action' => OmOrder::WAIT_SUPPLIER_CODE,
        'Magenest_OrderManagement::customer_service_wfs' => 'new'
    ];

    /**
     * Constructor.
     *
     * @param Action\Context $context
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param OrderRepositoryInterface $orderRepository
     * @param OmOrder $omOrder
     * @param AuthorizationHelper $authorizationHelper
     * @param Session $adminSession
     */
    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter,
        OrderRepositoryInterface $orderRepository,
        OmOrder $omOrder,
        AuthorizationHelper $authorizationHelper,
        Session $adminSession
    )
    {
        $this->_omOrder = $omOrder;
        $this->_authorizationHelper = $authorizationHelper;
        $this->_orderRepository = $orderRepository;
        $this->_collectionFactory = $collectionFactory;
        $this->_filter = $filter;
        $this->_adminSession = $adminSession;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        try {
            $changedCount = $this->massAction($collection);
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $changedCount));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while updating the order(s).'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order/');
    }

    /**
     * Validate status
     *
     * @param $status
     * @return bool
     */
    protected function _validateStatus($status)
    {
        if (empty($status)) {
            return false;
        }

        if(!isset($this->_allowStatuses[static::ADMIN_RESOURCE])){
            throw new \UnexpectedValueException(__("Order action is not allowed."));
        }

        return ($status == $this->_allowStatuses[static::ADMIN_RESOURCE]);
    }

    /**
     * Mass action
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $collection
     * @return int
     */
    abstract public function massAction($collection);
}
