<?php

namespace Magenest\CustomSource\Model\Source\City;

use Magenest\Directory\Model\ResourceModel\City\CollectionFactory;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $cityCollection;

    protected $cityOptions = [];

    /**
     * Options constructor.
     * @param CollectionFactory $cityCollection
     */
    public function __construct(CollectionFactory $cityCollection)
    {
        $this->cityCollection = $cityCollection;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        if (empty($this->cityOptions)) {
            $this->cityOptions = $this->cityCollection->create()->toOptionArray();
        }
        return $this->cityOptions;
    }
}
