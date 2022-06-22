<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 11/06/2022
 * Time: 10:05
 */
declare(strict_types=1);

namespace Magenest\Customer\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class FillDataFullNameSalesOrder implements DataPatchInterface
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
        $quoteAddressTable = $connection->getTableName('quote_address');
        $salesOrderAddressTable = $connection->getTableName('sales_order_address');
        $connection->update(
            $quoteAddressTable,
            [
                'fullname' => $customerFulName
            ]
        );

        $connection->update(
            $salesOrderAddressTable,
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
