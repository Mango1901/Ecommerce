<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 28/10/2021
 * Time: 13:40
 */

namespace Magenest\AffiliateCatalogRule\Setup\Patch\Data;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddAffiliateCustomerGroup implements DataPatchInterface
{
    /** @var GroupRepositoryInterface */
    private $groupRepository;

    /** @var GroupInterfaceFactory */
    private $customerGroupFactory;

    /**
     * AddAffiliateCustomerGroup constructor.
     * @param GroupRepositoryInterface $groupRepository
     * @param GroupInterfaceFactory $groupFactory
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory  $groupFactory
    ) {
        $this->groupRepository = $groupRepository;
        $this->customerGroupFactory = $groupFactory;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $group = $this->customerGroupFactory->create();
        $group->setId(null);
        $group->setCode('Affiliate Customers');
        $group->setTaxClassId(3);
        $this->groupRepository->save($group);
    }
}
