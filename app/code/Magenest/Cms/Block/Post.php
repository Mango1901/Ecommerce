<?php

namespace Magenest\Cms\Block;

use FishPig\WordPress\Model\PostFactory;
use FishPig\WordPress\Model\TermFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Post extends Template
{
    protected $termFactory;

    protected $postFactory;

    protected $_resource;

    protected $timezone;

    public function __construct(
        TermFactory $termFactory,
        PostFactory $postFactory,
        ResourceConnection $resource,
        Context $context,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->_resource   = $resource;
        $this->termFactory = $termFactory;
        $this->postFactory = $postFactory;
        $this->timezone    = $timezone;
        parent::__construct($context, $data);
    }

    public function getNewPost()
    {
        $postCollection = $this->postFactory->create()->getCollection()
            ->addMetaFieldToSelect('sort_order_number')
            ->addMetaFieldToFilter('sort_order_number', ['neq' => 'NULL'])
            ->addIsPublishedFilter()
            ->addMetaFieldToSort('sort_order_number', 'DESC')
            ->setPageSize(5)
            ->setCurPage(1);
        if ($postCollection->getSize() > 0) {
            return $postCollection;
        } else {
            return $this->postFactory->create()->getCollection()
                ->addIsPublishedFilter()
                ->setOrder('main_table.post_date', 'DESC')
                ->setPageSize(5)
                ->setCurPage(1);
        }
    }

    public function getFormatDate($dataTime)
    {
        return $this->timezone->date($dataTime)->format("M d - h:i A");
    }
}
