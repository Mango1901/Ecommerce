<?php

namespace Magenest\SellOnInstagram\Ui\Component\Listing\Column\Feed;

use Magento\Framework\Data\OptionSourceInterface;
use Magenest\SellOnInstagram\Model\ResourceModel\Mapping\CollectionFactory as FeedTemplateCollectionFactory;


class FeedTemplate implements OptionSourceInterface
{
    /**
     * @var FeedTemplateCollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        FeedTemplateCollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->collectionFactory->create()->toOptionArray();
    }
}
