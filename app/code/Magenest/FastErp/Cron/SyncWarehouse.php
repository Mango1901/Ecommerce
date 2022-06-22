<?php

namespace Magenest\FastErp\Cron;

use Magenest\FastErp\Api\UpdateWarehouseInformationInterface;
use Magento\Framework\Notification\NotifierInterface;

class SyncWarehouse
{
    protected $notifier;

    /**
     * @var UpdateWarehouseInformationInterface
     */
    protected $updateWarehouseInformation;

    /**
     * SyncWarehouse constructor.
     *
     * @param NotifierInterface $notifier
     * @param UpdateWarehouseInformationInterface $updateWarehouseInformation
     */
    public function __construct(
        NotifierInterface $notifier,
        UpdateWarehouseInformationInterface $updateWarehouseInformation
    ) {
        $this->notifier = $notifier;
        $this->updateWarehouseInformation = $updateWarehouseInformation;
    }

    public function execute()
    {
        $this->notifier->addNotice(
            'ERP Sync Warehouse',
            "Start syncing warehouses from ERP"
        );

        $result = $this->updateWarehouseInformation->execute();

        if ($result) {
            $this->notifier->addNotice(
                'ERP Sync Warehouse',
                "Complete syncing warehouses from ERP"
            );
        } else {
            $this->notifier->addNotice(
                'ERP Sync Warehouse',
                "Error when syncing warehouses from ERP. Please try again"
            );
        }
    }
}
