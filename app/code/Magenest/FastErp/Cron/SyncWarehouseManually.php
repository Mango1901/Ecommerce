<?php

namespace Magenest\FastErp\Cron;

use Magenest\FastErp\Api\UpdateWarehouseInformationInterface;
use Magento\Framework\Notification\NotifierInterface;
use Magenest\FastErp\Model\Flag\WarehousesFactory;

class SyncWarehouseManually
{
    /**
     * @var NotifierInterface
     */
    protected $notifier;

    /**
     * @var UpdateWarehouseInformationInterface
     */
    protected $updateWarehouseInformation;

    /**
     * @var WarehousesFactory
     */
    protected $warehousesFactory;

    /**
     * SyncStock constructor.
     *
     * @param NotifierInterface $notifier
     * @param WarehousesFactory $stockFlag
     * @param UpdateWarehouseInformationInterface $updateWarehouseInformation
     */
    public function __construct(
        NotifierInterface $notifier,
        WarehousesFactory $warehousesFactory,
        UpdateWarehouseInformationInterface $updateWarehouseInformation
    ) {
        $this->warehousesFactory = $warehousesFactory;
        $this->notifier = $notifier;
        $this->updateWarehouseInformation = $updateWarehouseInformation;
    }

    public function execute()
    {
        $flag = $this->warehousesFactory->create()->loadSelf();

        if ($flag->getState() == 1) {
            $flag->setState(2)->save();
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
            $flag->setState(0)->save();
        }
    }
}
