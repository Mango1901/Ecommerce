<?php

namespace Magenest\RewardPoints\Block\Adminhtml;

/**
 * Class Grid
 * @package Magenest\RewardPoints\Block\Adminhtml
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_template = 'Magenest_RewardPoints::customer_transaction_tab.phtml';

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magenest\RewardPoints\Model\ResourceModel\Transaction\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magenest\RewardPoints\Model\ResourceModel\Transaction\Grid\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->setEmptyText(__('No Transaction Found'));
        $this->setId('transaction_tab');
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param $customerId
     *
     * @return mixed
     */
    public function getTransactionCollection($customerId)
    {
        $transactionCollection = $this->collectionFactory->create();
        $transactionCollection->addFieldToSelect('*');
        $transactionCollection->addFieldToFilter('customer_id', array('eq' => $customerId));

        return $transactionCollection;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $transactionCollection = $this->getTransactionCollection($this->getCustomerId());
        $this->setCollection($transactionCollection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'id',
            [
                'header' => __('Transaction Id'),
                'index'  => 'id'
            ]
        );

        $this->addColumn(
            'rule_id',
            [
                'header'   => __('Rule ID'),
                'index'    => 'rule_id',
                'renderer' => 'Magenest\RewardPoints\Block\Adminhtml\TitleRenderer'
            ]
        );

        $this->addColumn(
            'points_change',
            [
                'header' => __('Points Change'),
                'index'  => 'points_change'
            ]
        );

        $this->addColumn(
            'points_after',
            [
                'header' => __('Points After'),
                'index'  => 'points_after'
            ]
        );

        $this->addColumn(
            'insertion_date',
            [
                'header' => __('Insertion Date'),
                'index'  => 'insertion_date',
                'renderer' => 'Magenest\RewardPoints\Block\Adminhtml\FormatDate'
            ]
        );

        $this->addColumn(
            'expiry_date',
            [
                'header' => __('Expiry Date'),
                'index'  => 'expiry_date',
                'renderer' => 'Magenest\RewardPoints\Block\Adminhtml\ExpiryDate'
            ]
        );

        $this->addColumn(
            'comment',
            [
                'header' => __('Comment'),
                'index'  => 'comment'
            ]
        );

        return $this;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'add_point',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)->setData(
                [
                    'label' => __('Add Point'),
                    'class' => 'task action-secondary primary',
                    'id'    => 'add_point'
                ]
            )->setDataAttribute(
                [
                    'action' => 'add_point'
                ]
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = $this->getAddPointHtml();
        if ($this->getFilterVisibility()) {
            $html .= $this->getResetFilterButtonHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getAddPointHtml()
    {
        return $this->getChildHtml('add_point');
    }

    /**
     * @return string
     */
    public function getAddPointUrl()
    {
        return $this->getUrl('rewardpoints/transaction/save');
    }
}
