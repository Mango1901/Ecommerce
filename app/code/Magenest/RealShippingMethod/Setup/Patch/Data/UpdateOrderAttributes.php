<?php
namespace Magenest\RealShippingMethod\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;

class UpdateOrderAttributes implements DataPatchInterface
{
    const SHIPPING_FEE         = "shipping_fee";
    const SOURCE_CODE          = "source_code";
    const REAL_SHIPPING_METHOD = "real_shipping_method";
    const AUTHORIZED_ADMIN     = "authorized_admin";
    const API_ORDER_ID         = "api_order_id";

    /** @var ModuleDataSetupInterface */
    private $setup;

    /** @var SalesSetupFactory */
    private $salesSetupFactory;

    /**
     * @param ModuleDataSetupInterface $setup
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        SalesSetupFactory        $salesSetupFactory
    ) {
        $this->setup             = $setup;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $this->setup]);
        $this->addShippingFeeAttribute($salesSetup);
        $this->addSourceCodeAttribute($salesSetup);
        $this->addRealShippingMethodAttribute($salesSetup);
        $this->addAuthorizedAdminAttribute($salesSetup);
        $this->addApiOrderIdAttribute($salesSetup);
    }

    /**
     * @param SalesSetup $eavSetup
     */
    private function addShippingFeeAttribute(SalesSetup $eavSetup)
    {
        $config = ['type' => 'static', 'default' => ''];
        if (!$eavSetup->getAttribute(Order::ENTITY, self::SHIPPING_FEE)) {
            $eavSetup->addAttribute(Order::ENTITY, self::SHIPPING_FEE, $config);
        } else {
            $eavSetup->updateAttribute(Order::ENTITY, self::SHIPPING_FEE, $config);
        }
    }

    /**
     * @param SalesSetup $eavSetup
     */
    private function addSourceCodeAttribute(SalesSetup $eavSetup)
    {
        $config = ['type' => 'static', 'default' => ''];
        if (!$eavSetup->getAttribute(Order::ENTITY, self::SOURCE_CODE)) {
            $eavSetup->addAttribute(Order::ENTITY, self::SOURCE_CODE, $config);
        } else {
            $eavSetup->updateAttribute(Order::ENTITY, self::SOURCE_CODE, $config);
        }
    }

    /**
     * @param SalesSetup $eavSetup
     */
    private function addRealShippingMethodAttribute(SalesSetup $eavSetup)
    {
        $config = ['type' => 'static', 'default' => ''];
        if (!$eavSetup->getAttribute(Order::ENTITY, self::REAL_SHIPPING_METHOD)) {
            $eavSetup->addAttribute(Order::ENTITY, self::REAL_SHIPPING_METHOD, $config);
        } else {
            $eavSetup->updateAttribute(Order::ENTITY, self::REAL_SHIPPING_METHOD, $config);
        }
    }

    /**
     * @param SalesSetup $eavSetup
     */
    private function addAuthorizedAdminAttribute(SalesSetup $eavSetup)
    {
        $config = ['type' => 'static', 'default' => ''];
        if (!$eavSetup->getAttribute(Order::ENTITY, self::AUTHORIZED_ADMIN)) {
            $eavSetup->addAttribute(Order::ENTITY, self::AUTHORIZED_ADMIN, $config);
        } else {
            $eavSetup->updateAttribute(Order::ENTITY, self::AUTHORIZED_ADMIN, $config);
        }
    }

    /**
     * @param SalesSetup $eavSetup
     */
    private function addApiOrderIdAttribute(SalesSetup $eavSetup)
    {
        $config = ['type' => 'static', 'default' => ''];
        if (!$eavSetup->getAttribute(Order::ENTITY, self::API_ORDER_ID)) {
            $eavSetup->addAttribute(Order::ENTITY, self::API_ORDER_ID, $config);
        } else {
            $eavSetup->updateAttribute(Order::ENTITY, self::API_ORDER_ID, $config);
        }
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
}
