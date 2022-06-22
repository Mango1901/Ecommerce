<?php


namespace Magenest\Affiliate\Block\Adminhtml\Customer;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class CustomerGrid
 *
 * @package Magenest\RewardPoints\Block\Adminhtml\Transaction
 */
class Grid extends Extended
{
    const CREATE_ACCOUNT_ACTION = 'create_account';
    const CREATE_WITHDRAW_ACTION = 'create_transaction';
    const CREATE_TRANSACTION_ACTION = 'create_withdraw';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var CollectionFactory
     */
    protected $customerGroup;

    /**
     * CustomerGrid constructor.
     *
     * @param Context           $context
     * @param Data              $backendHelper
     * @param CustomerFactory   $customerFactory
     * @param CollectionFactory $customerGroup
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CustomerFactory $customerFactory,
        CollectionFactory $customerGroup,
        array $data = []
    ) {
        $this->_customerFactory = $customerFactory;
        $this->customerGroup    = $customerGroup;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @throws FileSystemException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer-grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_customerFactory->create()->getCollection();
        $action     = $this->getRequest()->getParam('action');
        switch ($action) {
            case self::CREATE_ACCOUNT_ACTION:
                $accountCollection = ObjectManager::getInstance()
                    ->create('Magenest\Affiliate\Model\ResourceModel\Account\Collection');
                if ($accountCollection->getSize()) {
                    $collection->addFieldToFilter(
                        'entity_id',
                        ['nin' => $accountCollection->getColumnValues('customer_id')]
                    );
                }
                break;
            case self::CREATE_TRANSACTION_ACTION:
                $collection->getSelect()->joinRight(
                    ['aff' => $collection->getTable('magenest_affiliate_account')],
                    'e.entity_id = aff.customer_id',
                    ['balance', 'status', 'created_at']
                );
                break;
            case self::CREATE_WITHDRAW_ACTION:
                $collection->getSelect()->joinInner(
                    ['aff' => $collection->getTable('magenest_affiliate_account')],
                    'e.entity_id = aff.customer_id AND aff.balance > 0 ',
                    ['balance', 'status', 'created_at']
                );
                break;

            default:
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $action = $this->getRequest()->getParam('action');

        $this->addColumn('customer_id', [
            'header_css_class' => 'a-center',
            'type'             => 'radio',
            'html_name'        => 'customer_id',
            'align'            => 'center',
            'index'            => 'entity_id',
            'filter'           => false,
            'sortable'         => false
        ]);
        $this->addColumn('entity_id', [
            'header'   => __('ID'),
            'sortable' => true,
            'index'    => 'entity_id'
        ]);
        $this->addColumn('firstname', [
            'header'   => __('First Name'),
            'index'    => 'firstname',
            'type'     => 'text',
            'sortable' => true,
        ]);
        $this->addColumn('lastname', [
            'header'   => __('Last Name'),
            'index'    => 'lastname',
            'type'     => 'text',
            'sortable' => true,
        ]);
        $this->addColumn('email', [
            'header'   => __('Email'),
            'index'    => 'email',
            'type'     => 'text',
            'sortable' => true,
        ]);
        if ($action === self::CREATE_TRANSACTION_ACTION|| $action === self::CREATE_WITHDRAW_ACTION) {
            $this->addColumn('balance', [
                'header'   => __('Balance'),
                'index'    => 'balance',
                'type'     => 'text',
                'sortable' => true,
                'renderer' => \Magento\Backend\Block\Widget\Grid\Column\Renderer\Price::class,
            ]);
        }
        $this->addColumn('group_id', [
            'header'   => __('Group'),
            'index'    => 'group_id',
            'type'     => 'options',
            'options'  => $this->customerGroup->create()->toOptionHash(),
            'sortable' => true,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('affiliate/customer/grid', ['_current' => true]);
    }
}
