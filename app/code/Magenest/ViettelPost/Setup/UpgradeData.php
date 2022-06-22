<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\ViettelPost\Setup;

use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 * @package Magenest\ViettelPost\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    const SHIPMENT_CARRIER_NAME = 'carrier_name';

    public function __construct(
        SalesSetupFactory $salesSetupFactory
    )
    {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    protected $salesSetupFactory;

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->addNewColumnShippingCarrierToSaleShipmentTable($installer);
        }

        $installer->endSetup();
    }

    private function addNewColumnShippingCarrierToSaleShipmentTable($setup){
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        $salesInstaller->addAttribute('shipment', self::SHIPMENT_CARRIER_NAME,
            [
                'type' => 'text',
                'length'=> 255,
                'visible' => false,
                'nullable' => true,
            ]
        );
    }

}
