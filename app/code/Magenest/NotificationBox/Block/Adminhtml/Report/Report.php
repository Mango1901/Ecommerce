<?php

namespace Magenest\NotificationBox\Block\Adminhtml\Report;

use Magenest\NotificationBox\Model\CustomerToken;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\CollectionFactory;
use Magento\Backend\Block\Template;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface as DateTime;

class Report extends Template
{

    /** @var DateTime  */
    protected $dateTime;

    /** @var CollectionFactory  */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Template\Context $context
     * @param DateTime $dateTime
     * @param array $data
     */
    public function __construct(CollectionFactory $collectionFactory, Template\Context $context, DateTime $dateTime, array $data = [])
    {
        $this->collectionFactory = $collectionFactory;
        $this->dateTime = $dateTime;
        parent::__construct($context, $data);
    }

    public function getTotalSubscribers()
    {
        return $this->collectionFactory->create()->addFieldToFilter('status', CustomerToken::STATUS_SUBSCRIBED)->count();
    }

    public function getTotalUnSubscribers()
    {
        return $this->collectionFactory->create()->addFieldToFilter('status', CustomerToken::STATUS_UNSUBSCRIBED)->count();
    }

    public function getTimeNow()
    {
        return $this->dateTime->date()->format('Y-m-d');
    }

    public function getTimeBefore7Day()
    {
        return date('Y-m-d', strtotime('-' . 7 . 'day', strtotime($this->getTimeNow())));
    }
}
