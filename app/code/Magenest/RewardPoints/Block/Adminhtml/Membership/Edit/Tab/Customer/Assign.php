<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Membership\Edit\Tab\Customer;

use Exception;
use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Model\Membership;
use Magenest\RewardPoints\Model\MembershipFactory;
use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory as MembershipCollection;
use Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer\CollectionFactory as MembershipCustomerCollection;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Class Assign
 *
 * @package Magenest\RewardPoints\Block\Adminhtml\Membership\Edit\Tab\Customer
 */
class Assign extends Extended
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_massactionBlockName = \Magenest\RewardPoints\Block\Widget\Grid\Massaction\Extended::class;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var CustomerCollection
     */
    protected $_customerCollection;

    /**
     * @var MembershipCollection
     */
    protected $_membershipCollection;

    /**
     * @var MembershipCustomerCollection
     */
    protected $_membershipCustomerCollection;

    /**
     * @var Membership|null
     */
    private $model = null;

    /**
     * Assign constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param CustomerFactory $customerFactory
     * @param CustomerCollection $customerCollection
     * @param Registry $coreRegistry
     * @param MembershipCustomerCollection $membershipCustomerCollection
     * @param MembershipCollection $membershipCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CustomerFactory $customerFactory,
        CustomerCollection $customerCollection,
        Registry $coreRegistry,
        MembershipCustomerCollection $membershipCustomerCollection,
        MembershipCollection $membershipCollectionFactory,
        array $data = []
    ) {
        $this->_membershipCollection = $membershipCollectionFactory;
        $this->_customerFactory = $customerFactory;
        $this->_customerCollection = $customerCollection;
        $this->_coreRegistry = $coreRegistry;
        $this->_membershipCustomerCollection = $membershipCustomerCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('membership_customer');
        $this->setDefaultSort('in_group');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setDefaultLimit(30);
        $this->setRowClickCallback(null);

    }

    /**
     * @return Extended
     * @throws LocalizedException
     */
    protected function _prepareCollection()
    {
        $collection = $this->_customerCollection->create();
        $collection->addAttributeToSelect(
            ['*']
        );
        $collection
            ->joinField(
                'added_at',
                $collection->getTable('magenest_rewardpoints_membership_customer'),
                'updated_at',
                'customer_id=entity_id',
                "membership_id = {$this->getMembershipModel()->getId()}",
                'left'
            )
            ->joinField(
                'membership_id',
                $collection->getTable('magenest_rewardpoints_membership_customer'),
                'membership_id',
                'customer_id=entity_id',
                null,
                'left'
            )
            ->joinField(
                'membership_name',
                $collection->getTable('magenest_rewardpoints_membership'),
                'name',
                'id = membership_id',
                null,
                'left'
            );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @param Product|DataObject $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '';
    }

    /**
     * @return $this|Assign
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer-checkbox');

        $customerIds = $this->getAssignedCustomer();
        $result = [];
        if (is_array($customerIds)) {
            foreach ($customerIds as $id) {
                $result[] = ['label' => $id, 'value' => $id];

            }
        }
        $this->getMassactionBlock()->setData('assigned_customer', $customerIds);

        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /**
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Id'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-data col-id',
                'column_css_class' => 'col-data col-id'
            ]
        );

        $this->addColumn(
            'firstname',
            [
                'header' => __('Firstname'),
                'sortable' => true,
                'index' => 'firstname',
                'header_css_class' => 'col-data col-firstname',
                'column_css_class' => 'col-data col-firstname'
            ]
        );

        $this->addColumn(
            'lastname',
            [
                'header' => __('Lastname'),
                'sortable' => true,
                'index' => 'lastname',
                'header_css_class' => 'col-data col-lastname',
                'column_css_class' => 'col-data col-lastname'
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'sortable' => true,
                'index' => 'email',
                'header_css_class' => 'col-data col-email',
                'column_css_class' => 'col-data col-email'
            ]
        );

        $this->addColumn(
            'membership_name',
            [
                'header' => __('Current Membership Group'),
                'sortable' => true,
                'index' => 'membership_name',
                'header_css_class' => 'col-data col-membership_name',
                'column_css_class' => 'col-data col-membership_name'
            ]
        );

        $this->addColumn(
            'added_at',
            [
                'header' => __('Added At'),
                'sortable' => true,
                'index' => 'added_at',
                'header_css_class' => 'col-data col-added_at',
                'column_css_class' => 'col-data col-added_at'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    public function getAssignedCustomer()
    {
        $membershipId = $this->getRequest()->getParam('id', 0);

        $membershipCustomer = $this->_membershipCustomerCollection->create();
        return $membershipCustomer->addFieldToFilter(MembershipCustomerInterface::MEMBERSHIP_ID, $membershipId)->getAllIds();
    }

    /**
     * @return Membership
     */
    private function getMembershipModel()
    {
        if (empty($this->model)) {
            $id = $this->getRequest()->getParam('id', 0);
            $this->model = $this->_membershipCollection->create()->addFieldToFilter(MembershipInterface::ENTITY_ID, $id)->getFirstItem();
            if (empty($this->model->getId())) {
                $this->model->setId(0);
            }
        }
        return $this->model;
    }
}
