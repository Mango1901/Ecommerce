<?php

namespace Magenest\RewardPoints\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Helper\Session\CurrentCustomer;

class MyReferral extends \Magento\Framework\View\Element\Template
{

    /** @var \Magenest\RewardPoints\Model\MyReferralFactory $_myReferralFactory */
    protected $_myReferralFactory;

    /** @var \Magento\Customer\Helper\Session\CurrentCustomer $_currentCustomer */
    protected $_currentCustomer;

    /**
     * MyReferral constructor.
     * @param \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory
     * @param CurrentCustomer $currentCustomer
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_myReferralFactory = $myReferralFactory;
        $collection = $this->_myReferralFactory->create()->getCollection();
        $this->setCollection($collection);
        $this->_currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
    }

    public function getCurrentCustomerId()
    {
        return $this->_currentCustomer->getCustomerId();
    }

    public function getMyReferrals()
    {
        $customerId = $this->getCurrentCustomerId();
        $myReferrals = $this->_myReferralFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->getItems();
        return $myReferrals;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'magenest.referafriend.record.pager'
            )->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $this->setChild('pager', $pager);// set pager block in layout
        }
        return $this;
    }

    public function getCollection()
    {
        $customerId = $this->getCurrentCustomerId();
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
        $collection = $this->_myReferralFactory->create()->getCollection()->addFieldToFilter('customer_id',
            $customerId);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);

        return $collection;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}