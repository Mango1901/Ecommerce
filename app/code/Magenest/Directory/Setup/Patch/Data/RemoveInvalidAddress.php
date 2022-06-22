<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Directory extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Directory
 */
namespace Magenest\Directory\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

class RemoveInvalidAddress implements \Magento\Framework\Setup\Patch\DataPatchInterface
{

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {

        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
      $connection = $this->resourceConnection->getConnection();
      $condition = $connection->prepareSqlCondition('country_id',['neq' => 'VN']);
      $connection->delete(
          $this->resourceConnection->getTableName('customer_address_entity'),
         $condition
      );
    }
}
