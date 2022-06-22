<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\OrderExtraInformation\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var SalesSetupFactory
     */
    protected $_salesSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    protected $_quoteSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->_quoteSetupFactory = $quoteSetupFactory;
        $this->_salesSetupFactory = $salesSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->_salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->_quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $this->addIsWholesaleAttribute($salesInstaller, $quoteInstaller);
        }

        $setup->endSetup();
    }

    private function addIsWholesaleAttribute(\Magento\Sales\Setup\SalesSetup $salesInstaller, \Magento\Quote\Setup\QuoteSetup $quoteInstaller)
    {
        $salesInstaller->addAttribute('order', 'is_wholesale_order', ['type' => 'boolean', 'nullable' => true, 'default' => 0]);
        $quoteInstaller->addAttribute('quote', 'is_wholesale_order', ['type' => 'boolean', 'nullable' => true, 'default' => 0]);
    }
}
