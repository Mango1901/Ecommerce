<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 21/06/2022
 * Time: 11:58
 */
declare(strict_types=1);

namespace Magenest\RewardPoints\Model;

use Magenest\RewardPoints\Api\IsEarnedPointFromClickInterface;
use Magento\Framework\App\ResourceConnection;

class IsEarnedPointFromClick implements IsEarnedPointFromClickInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function execute($customerId, $applyCustomerId)
    {
        $connection = $this->resourceConnection->getConnection();

        $refereeClickTrackTable = $connection->getTableName('referee_click_track');

        $select = $connection->select()->from(
            $refereeClickTrackTable
        )->where(
            'customer_id = ?',
            $customerId
        )->where(
            'apply_customer_id = ?',
            $applyCustomerId
        );

        $data = $connection->fetchOne($select);
        if ($data) {
            return true;
        }

        return false;
    }
}
