<?php

namespace Magenest\Customer\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class FillDataFullName implements DataPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function __construct(
        ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->setup->startSetup();
        $connection = $this->setup->getConnection();
        $customerFulName = $connection->getConcatSql(['lastname', 'firstname'], ' ');
        $customerEntityTable = $connection->getTableName('customer_entity');
        $customerAddressEntityTable = $connection->getTableName('customer_address_entity');
        $connection->update(
            $customerEntityTable,
            [
                'fullname' => $customerFulName
            ]
        );

        $connection->update(
            $customerAddressEntityTable,
            [
                'fullname' => $customerFulName
            ]
        );

        $this->setup->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [AddFullNameAttribute::class];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
