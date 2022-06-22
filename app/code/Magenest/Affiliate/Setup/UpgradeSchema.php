<?php

namespace Magenest\Affiliate\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Magenest\Affiliate\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $accountTable = $installer->getTable('magenest_affiliate_account');
            if ($accountTable && !$connection->tableColumnExists($accountTable, 'parent_email')) {
                $connection->addColumn($installer->getTable($accountTable), 'parent_email', [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => '255',
                    'nullable' => true,
                    'default'  => '',
                    'comment'  => 'Email of parent affiliate'
                ]);
            }

            $withdrawTable = $installer->getTable('magenest_affiliate_account');
            if (!$connection->tableColumnExists($withdrawTable, 'description')) {
                $connection->addColumn($installer->getTable($withdrawTable), 'description', [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => '255',
                    'nullable' => true,
                    'default'  => '',
                    'comment'  => 'Description transaction of withdraw history'
                ]);
            }
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $campaignTable = $installer->getTable('magenest_affiliate_campaign');
            if ($campaignTable && !$connection->tableColumnExists($campaignTable, 'apply_discount_on_tax')) {
                $connection->addColumn($installer->getTable($campaignTable), 'apply_discount_on_tax', [
                    'type'     => Table::TYPE_BOOLEAN,
                    'nullable' => true,
                    'default'  => '0',
                    'comment'  => 'Apply discount on tax'
                ]);
            }

            /** Add more column to table 'quote' */
            $quoteTable = $installer->getTable('quote');
            if (!$connection->tableColumnExists($quoteTable, 'affiliate_key')) {
                $connection->addColumn($quoteTable, 'affiliate_key', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment'  => 'Affiliate Key'
                ]);
            }

            if (!$connection->tableColumnExists($quoteTable, 'affiliate_discount_amount')) {
                $connection->addColumn($quoteTable, 'affiliate_discount_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Affiliate Discount Amount'
                ]);
            }

            if (!$connection->tableColumnExists($quoteTable, 'base_affiliate_discount_amount')) {
                $connection->addColumn($quoteTable, 'base_affiliate_discount_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Base Affiliate Discount Amount'
                ]);
            }

            if (!$connection->tableColumnExists($quoteTable, 'affiliate_commission')) {
                $connection->addColumn($quoteTable, 'affiliate_commission', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '255',
                    'comment'  => 'Affiliate Commission'
                ]);
            }

            if (!$connection->tableColumnExists($quoteTable, 'affiliate_shipping_commission')) {
                $connection->addColumn($quoteTable, 'affiliate_shipping_commission', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '255',
                    'comment'  => 'Affiliate Shipping Commission'
                ]);
            }

            if (!$connection->tableColumnExists($quoteTable, 'affiliate_discount_shipping_amount')) {
                $connection->addColumn($quoteTable, 'affiliate_discount_shipping_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Affiliate Discount Shipping Amount'
                ]);
            }

            if (!$connection->tableColumnExists($quoteTable, 'affiliate_base_discount_shipping_amount')) {
                $connection->addColumn($quoteTable, 'affiliate_base_discount_shipping_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Affiliate Base Discount Shipping Amount'
                ]);
            }

            /**
             * Add more column to table  'quote_item'
             */
            $quoteItemTable = $installer->getTable('quote_item');

            if (!$connection->tableColumnExists($quoteItemTable, 'affiliate_discount_amount')) {
                $connection->addColumn($quoteItemTable, 'affiliate_discount_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Affiliate Discount Amount'
                ]);
            }

            if (!$connection->tableColumnExists($quoteItemTable, 'base_affiliate_discount_amount')) {
                $connection->addColumn($quoteItemTable, 'base_affiliate_discount_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Base Affiliate Discount Amount'
                ]);
            }

            if (!$connection->tableColumnExists($quoteItemTable, 'affiliate_commission')) {
                $connection->addColumn($quoteItemTable, 'affiliate_commission', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '255',
                    'comment'  => 'Affiliate Commission'
                ]);
            }

            /** Add more column to table  'sales_order' */
            $orderTable = $installer->getTable('sales_order');

            if ($orderTable && !$connection->tableColumnExists($orderTable, 'affiliate_shipping_commission')) {
                $connection->addColumn($orderTable, 'affiliate_shipping_commission', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '255',
                    'comment'  => 'affiliate_shipping_commission'
                ]);
            }

            if ($orderTable
                && !$connection->tableColumnExists(
                    $orderTable,
                    'affiliate_earn_commission_invoice_after'
                )) {
                $connection->addColumn($orderTable, 'affiliate_earn_commission_invoice_after', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '255',
                    'comment'  => 'affiliate_shipping_commission'
                ]);
            }

            if ($orderTable && !$connection->tableColumnExists($orderTable, 'affiliate_discount_shipping_amount')) {
                $connection->addColumn($orderTable, 'affiliate_discount_shipping_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Affiliate Discount Shipping Amount'
                ]);
            }

            if (!$connection->tableColumnExists($orderTable, 'affiliate_discount_invoiced')) {
                $connection->addColumn($orderTable, 'affiliate_discount_invoiced', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Affiliate Discount Invoiced'
                ]);
            }

            if (!$connection->tableColumnExists($orderTable, 'base_affiliate_discount_invoiced')) {
                $connection->addColumn($orderTable, 'base_affiliate_discount_invoiced', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Base Affiliate Discount Invoiced'
                ]);
            }

            if (!$connection->tableColumnExists($orderTable, 'affiliate_discount_refunded')) {
                $connection->addColumn($orderTable, 'affiliate_discount_refunded', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Affiliate Discount Refunded'
                ]);
            }

            if (!$connection->tableColumnExists($orderTable, 'base_affiliate_discount_refunded')) {
                $connection->addColumn($orderTable, 'base_affiliate_discount_refunded', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Base Affiliate Discount Refunded'
                ]);
            }

            if (!$connection->tableColumnExists($orderTable, 'affiliate_commission_invoiced')) {
                $connection->addColumn($orderTable, 'affiliate_commission_invoiced', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '255',
                    'comment'  => 'Affiliate Commission Invoiced'
                ]);
            }

            if (!$connection->tableColumnExists($orderTable, 'affiliate_commission_refunded')) {
                $connection->addColumn($orderTable, 'affiliate_commission_refunded', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '255',
                    'comment'  => 'Affiliate Commission Refund'
                ]);
            }

            /** Add more column to table  'sales_order_item' */
            $orderItemTable = $installer->getTable('sales_order_item');

            if (!$connection->tableColumnExists($orderItemTable, 'affiliate_discount_amount')) {
                $connection->addColumn($orderItemTable, 'affiliate_discount_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Sales Order Item'
                ]);
            }

            if (!$connection->tableColumnExists($orderItemTable, 'base_affiliate_discount_amount')) {
                $connection->addColumn($orderItemTable, 'base_affiliate_discount_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Base Affiliate Discount Amount'
                ]);
            }

            if (!$connection->tableColumnExists($orderItemTable, 'affiliate_commission')) {
                $connection->addColumn($orderItemTable, 'affiliate_commission', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '255',
                    'comment'  => 'Affiliate Commission'
                ]);
            }
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $campaignTable = $installer->getTable('magenest_affiliate_campaign');
            if ($campaignTable && !$connection->tableColumnExists($campaignTable, 'customer_group_ids')) {
                $connection->addColumn($installer->getTable($campaignTable), 'customer_group_ids', [
                    'type'    => Table::TYPE_TEXT,
                    'length'  => '255',
                    'default' => '0,1',
                    'comment' => 'Customer Group Ids'
                ]);
            }

            if ($campaignTable && !$connection->tableColumnExists($campaignTable, 'stop_rules_processing')) {
                $connection->addColumn($installer->getTable($campaignTable), 'stop_rules_processing', [
                    'type'    => Table::TYPE_SMALLINT,
                    'default' => '0',
                    'comment' => 'Discard subsequent rules'
                ]);
            }

            /** Add more column to table 'quote_address' */
            $addressTable = $installer->getTable('quote_address');

            if (!$connection->tableColumnExists($addressTable, 'affiliate_discount_amount')) {
                $connection->addColumn($addressTable, 'affiliate_discount_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Affiliate Discount Amount'
                ]);
            }

            if (!$connection->tableColumnExists($addressTable, 'base_affiliate_discount_amount')) {
                $connection->addColumn($addressTable, 'base_affiliate_discount_amount', [
                    'type'     => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'comment'  => 'Base Affiliate Discount Amount'
                ]);
            }

            $orderTable = $installer->getTable('sales_order');
            if ($orderTable
                && !$connection->tableColumnExists(
                    $orderTable,
                    'base_affiliate_discount_shipping_amount'
                )) {
                $connection->addColumn(
                    $installer->getTable($orderTable),
                    'base_affiliate_discount_shipping_amount',
                    [
                        'type'     => Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length'   => '12,4',
                        'comment'  => 'Base Affiliate Discount Shipping Amount'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $withdrawTable = $installer->getTable('magenest_affiliate_withdraw');
            $connection->addColumn($withdrawTable, 'withdraw_description', [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'length'   => '1M',
                'comment'  => 'Withdraw Description'
            ]);
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $campaignTable = $installer->getTable('magenest_affiliate_campaign');
            if ($campaignTable && !$connection->tableColumnExists($campaignTable, 'code_length')) {
                $connection->addColumn($installer->getTable($campaignTable), 'code_length', [
                    'type'    => Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'comment'  => 'Code Length'
                ]);
            }

            if ($campaignTable && !$connection->tableColumnExists($campaignTable, 'code_format')) {
                $connection->addColumn($installer->getTable($campaignTable), 'code_format', [
                    'type'    => Table::TYPE_TEXT,
                    'length'   => '255',
                    'comment'  => 'Code Format'
                ]);
            }

            if ($campaignTable && !$connection->tableColumnExists($campaignTable, 'coupon_code')) {
                $connection->addColumn($installer->getTable($campaignTable), 'coupon_code', [
                    'type'    => Table::TYPE_TEXT,
                    'length'   => '255',
                    'comment'  => 'Coupon Code'
                ]);
            }

            if ($campaignTable && $connection->tableColumnExists($campaignTable, 'customer_group_ids')) {
                $connection->dropColumn($installer->getTable($campaignTable), 'customer_group_ids');
            }
        }

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $quoteTable = $installer->getTable('quote');
            if (!$connection->tableColumnExists($quoteTable, 'base_affiliate_discount_shipping_amount')) {
                $connection->changeColumn(
                    $installer->getTable($quoteTable),
                    'affiliate_base_discount_shipping_amount',
                    'base_affiliate_discount_shipping_amount',
                    [
                        'type'     => Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length'   => '12,4',
                        'comment'  => 'Base Affiliate Discount Shipping Amount'
                    ]
                );
            }

            $connection->modifyColumn($installer->getTable('magenest_affiliate_account'), 'group_id', [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => false,
                'unsigned' => true,
                'default'  => '1',
                'comment'  => 'Account Affiliate Group'
            ]);

            $connection->addForeignKey(
                $installer->getFkName(
                    'magenest_affiliate_account',
                    'group_id',
                    'magenest_affiliate_group',
                    'group_id'
                ),
                $installer->getTable('magenest_affiliate_account'),
                'group_id',
                $installer->getTable('magenest_affiliate_group'),
                'group_id',
                AdapterInterface::FK_ACTION_NO_ACTION
            );
        }

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            if ($installer->tableExists('magenest_affiliate_banner')) {
                $connection->dropTable($installer->getTable('magenest_affiliate_banner'));
            }
            $table = $connection->newTable($installer->getTable('magenest_affiliate_banner'))
                ->addColumn(
                    'banner_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                    'Banner ID'
                )
                ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable => false'], 'Banner Title')
                ->addColumn('content', Table::TYPE_TEXT, 255, [], 'Banner Content')
                ->addColumn('link', Table::TYPE_TEXT, 255, [], 'Banner Link')
                ->addColumn('status', Table::TYPE_INTEGER, null, ['nullable => false'], 'Banner Status')
                ->addColumn('rel_nofollow', Table::TYPE_INTEGER, 255, [], 'Banner Rel Nofollow')
                ->addColumn('campaign_id', Table::TYPE_INTEGER, null, ['nullable => false'], 'Banner Campaign ID')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Banner Created At')
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Banner Updated At'
                )->setComment('Banner Table');

            $connection->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $bannerTable = $installer->getTable('magenest_affiliate_banner');
            if ($connection->tableColumnExists($bannerTable, 'content')) {
                $connection->modifyColumn(
                    $bannerTable,
                    'content',
                    [
                        'type'    => Table::TYPE_TEXT,
                        'size'    => '2M',
                        'comment' => 'Banner Content'
                    ]
                );
            }
        }

        $installer->endSetup();
    }
}
