<?php

namespace Magenest\PaymentEPay\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class AddPendingPaidOrderStatus
 */
class UpdatePendingPaidOrderStatus implements DataPatchInterface
{
    const STATUS_CODE = 'pending_paid';
    const STATUS_LABEL = 'Pending_Paid';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UpdateOrderStatus constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        // Update Visible On Storefront Of Pending Payment
        $this->moduleDataSetup->getConnection()->update(
            $this->moduleDataSetup->getTable('sales_order_status'),
            [
                'status' => self::STATUS_CODE,
                'label' => self::STATUS_LABEL
            ],
            ['status = ?' => AddPendingPaidOrderStatus::STATUS_CODE]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [AddPendingPaidOrderStatus::class];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
