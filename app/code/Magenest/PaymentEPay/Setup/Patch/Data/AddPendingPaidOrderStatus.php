<?php

namespace Magenest\PaymentEPay\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

/**
 * Class AddPendingPaidOrderStatus
 */
class AddPendingPaidOrderStatus implements DataPatchInterface
{
    const STATUS_CODE = 'pending-paid';
    const STATUS_LABEL = 'Pending-Paid';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * @var StatusResourceFactory
     */
    protected $statusResourceFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $status = $this->statusFactory->create();

        $status->setData([
            'status' => self::STATUS_CODE,
            'label' => self::STATUS_LABEL,
        ]);

        /**
         * Save the new status
         */
        $statusResource = $this->statusResourceFactory->create();
        $statusResource->save($status);

        /**
         * Assign status to state
         */
        $status->assignState(Order::STATE_PROCESSING, false, true);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
