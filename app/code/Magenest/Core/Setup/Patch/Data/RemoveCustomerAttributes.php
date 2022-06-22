<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 08/11/2021
 * Time: 10:41
 */

namespace Magenest\Core\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RemoveCustomerAttributes implements DataPatchInterface
{
    private $moduleDataSetup;

    protected $customerSetupFactory;

    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(Customer::ENTITY, 'is_b2b_account');
        $customerSetup->removeAttribute(Customer::ENTITY, 'activation_status');
        $customerSetup->removeAttribute(Customer::ENTITY, 'bank_account');
        $customerSetup->removeAttribute(Customer::ENTITY, 'onesignal_player_id');
        $customerSetup->removeAttribute(Customer::ENTITY, 'b2b_expired_at');
    }

    public function getAliases()
    {
        return [];
    }
}
