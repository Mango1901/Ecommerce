<?php
namespace Magenest\AffiliateCatalogRule\Model\Config\Source;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Config\Source\Group;
use Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface;
use Magento\Framework\Convert\DataObject;

class CustomerGroups extends Group
{
    /** @var GroupSourceLoggedInOnlyInterface */
    private $groupSourceLoggedInOnly;

    /**
     * @param GroupManagementInterface $groupManagement
     * @param DataObject $converter
     * @param GroupSourceLoggedInOnlyInterface $groupSourceForLoggedInCustomers
     */
    public function __construct(
        GroupManagementInterface         $groupManagement,
        DataObject                       $converter,
        GroupSourceLoggedInOnlyInterface $groupSourceForLoggedInCustomers
    ) {
        parent::__construct($groupManagement, $converter, $groupSourceForLoggedInCustomers);
        $this->groupSourceLoggedInOnly = $groupSourceForLoggedInCustomers;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->groupSourceLoggedInOnly->toOptionArray();
        }

        return $this->_options;
    }
}
