<?php

namespace Magenest\SalesPerson\Plugin\Adminhtml;

use Magenest\OrderCancel\Model\Order\Source\AdminCancelReason;
use Magenest\SalesPerson\Model\Order\Source\AssignedToSales;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\Order;

class ViewOrder
{
    const BUTTONS = ['order_edit','send_notification','order_hold','order_invoice','order_ship','order_reorder','order_confirm_btn','order_cancel_with_reason','order_assigned_to_person','order_creditmemo'];
    /** @var SerializerInterface */
    private $serializer;

    /** @var AdminCancelReason */
    private $reasonOption;

    /** @var Registry */
    private $registry;
    /**
     * @var AssignedToSales
     */
    protected $assigntoSalesModel;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Backend\Block\Widget\Button\ButtonList
     */
    protected $buttonList;

    /**
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     * @param AssignedToSales $assignedToSalesModel
     * @param Registry $registry
     * @param AdminCancelReason $cancelReason
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList,
        AssignedToSales $assignedToSalesModel,
        Registry $registry,
        AdminCancelReason $cancelReason,
        \Magento\Backend\Model\Auth\Session $authSession,
        SerializerInterface $serializer
    ) {
        $this->buttonList = $buttonList;
        $this->authSession = $authSession;
        $this->assigntoSalesModel = $assignedToSalesModel;
        $this->registry = $registry;
        $this->serializer = $serializer;
        $this->reasonOption = $cancelReason;
    }

    /**
     * Before set layout
     *
     * @param View $subject
     * @param LayoutInterface $layout
     * @return array
     */
    public function beforeSetLayout(View $subject, LayoutInterface $layout)
    {
        $order = $this->getOrder();
        $this->handleAssignedToButton($subject, $order);

        return [$layout];
    }

    /**
     * Retrieve order model object
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->registry->registry('sales_order');
    }

    /**
     * @param View $subject
     * @param Order $order
     */
    protected function handleAssignedToButton(View $subject, Order $order)
    {
        $onclickJs = 'jQuery(\'#order_assigned_to_person\').om(\'showConfirmAssignedToPersonDialog\', \''
                . __('Please assign order to specific sales person?')
                . '\', \''
                . $subject->getUrl('salesperson/order/assignedtosales')
                . '\', \''
                . $this->serializer->serialize($this->assigntoSalesModel->toOptionArray())
                . '\');';

        if ($this->authSession->getUser()->getRole()->getId() == 1) {
            $subject->addButton(
                'order_assigned_to_person',
                [
                    'label' => __('Order Assigned To Person'),
                    'class' => 'cancel primary action-secondary',
                    'id' => 'order_assigned_to_person',
                    'onclick' => $onclickJs,
                    'data_attribute' => [
                        'mage-init' => '{"Magenest_SalesPerson/js/om": {}}'
                    ]
                ]
            );
        }


        if ($this->authSession->getUser()->getRole()->getId() != 1 && ($order->getData("assigned_to") != $this->authSession->getUser()->getId())) {
            $buttonArrays = self::BUTTONS;
            foreach ($buttonArrays as $one) {
                $subject->removeButton($one);
            }
        }
    }
}
