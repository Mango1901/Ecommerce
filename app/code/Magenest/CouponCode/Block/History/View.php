<?php

namespace Magenest\CouponCode\Block\History;

use Magento\Framework\App\Http\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon\Collection;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon\CollectionFactory;
use Magento\Theme\Block\Html\Pager;

/**
 * Class View
 */
class View extends Template
{
    /**
     * @var CollectionFactory
     */
    private $claimCouponCollection;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var Context
     */
    private $httpContext;

    /**
     * @param Template\Context $context
     * @param CollectionFactory $claimCouponCollection
     * @param DateTime $date
     * @param Context $httpContext
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $claimCouponCollection,
        DateTime $date,
        Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->claimCouponCollection = $claimCouponCollection;
        $this->date = $date;
        $this->httpContext = $httpContext;
    }

    /**
     * @inheirtDoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getHistoryCollection()) {
            $pager = $this->getLayout()->createBlock(
                Pager::class,
                'voucher.history.pager',
                ['']
            )->setShowPerPage(true)->setCollection(
                $this->getHistoryCollection()
            );
            $this->setChild('pager', $pager);
            $this->getHistoryCollection()->load();
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getHistoryCollection()
    {
        $page = $this->getRequest()->getParam('p') ?? 1;
        $pageSize = $this->getRequest()->getParam('limit') ?? 10;

        $collection = $this->claimCouponCollection->create();
        $customerId = $this->httpContext->getValue('customer_id');
        $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);

        if ($this->isActiveTabUsed()) {
            $collection->addFieldToFilter('times_used_per_customer', ['gteq' => 1]);
        } else {
            $today = $this->date->date('Y-m-d');
            $collection->addFieldToFilter('to_date', ['lteq' => $today]);
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return bool
     */
    public function isActiveTabUsed()
    {
        return $this->getRequest()->getParam('status') && $this->getRequest()->getParam('status') == 2;
    }

    /**
     * @return bool
     */
    public function isActiveTabExpired()
    {
        return !$this->getRequest()->getParam('status') ||
            (
                $this->getRequest()->getParam('status') &&
                $this->getRequest()->getParam('status') == 1
            );
    }

    /**
     * @return bool
     */
    public function getHref()
    {
        return $this->getRequest()->getParam('status') && $this->getRequest()->getParam('status') == 2;
    }
}
