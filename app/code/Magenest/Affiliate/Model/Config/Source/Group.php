<?php


namespace Magenest\Affiliate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magenest\Affiliate\Model\GroupFactory;

/**
 * Class Group
 * @package Magenest\Affiliate\Model\Config\Source
 */
class Group implements ArrayInterface
{
    /**
     * @var GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var
     */
    protected $_options;

    /**
     * Group constructor.
     *
     * @param GroupFactory $groupFactory
     */
    public function __construct(GroupFactory $groupFactory)
    {
        $this->_groupFactory = $groupFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options = [];
        $groupModel = $this->_groupFactory->create();
        $groupCollection = $groupModel->getCollection();
        foreach ($groupCollection as $item) {
            $data['value'] = $item->getId();
            $data['label'] = $item->getName();
            $this->_options[] = $data;
        }

        return $this->_options;
    }
}
