<?php


namespace Magenest\Affiliate\Model\Account;

use Magento\Framework\Option\ArrayInterface;
use Magenest\Affiliate\Model\GroupFactory;

/**
 * Class Group
 * @package Magenest\Affiliate\Model\Account
 */
class Group implements ArrayInterface
{
    /**
     * @type GroupFactory
     */
    protected $group;

    /**
     * Group constructor.
     *
     * @param GroupFactory $groupFactory
     */
    public function __construct(GroupFactory $groupFactory)
    {
        $this->group = $groupFactory;
    }

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $group = $this->getGroupCollection();
        $options = [];
        foreach ($group as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getName()
            ];
        }

        return $options;
    }

    /**
     * @return mixed
     */
    public function getGroupCollection()
    {
        $groupModel = $this->group->create();

        return $groupModel->getCollection();
    }
}
