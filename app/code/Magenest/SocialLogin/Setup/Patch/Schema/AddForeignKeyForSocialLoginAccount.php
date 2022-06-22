<?php

namespace Magenest\SocialLogin\Setup\Patch\Schema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Psr\Log\LoggerInterface;

/**
 * Add foreign key for magenest_social_login_account table reference to customer_entity table
 */
class AddForeignKeyForSocialLoginAccount implements SchemaPatchInterface
{
    const MAGENEST_SOCIAL_LOGIN_ACCOUNT_TABLE = 'magenest_social_login_account';
    const CUSTOMER_ID_COLUMN = 'customer_id';
    const CUSTOMER_ENTITY_TABLE = 'customer_entity';
    const ENTITY_ID = 'entity_id';

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddForeignKeyForSocialLoginAccount constructor.
     * @param SchemaSetupInterface $schemaSetup
     * @param ProductMetadataInterface $productMetadata
     * @param LoggerInterface $logger
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup,
        ProductMetadataInterface $productMetadata,
        LoggerInterface $logger
    ) {
        $this->schemaSetup = $schemaSetup;
        $this->productMetadata = $productMetadata;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        $connection = $this->schemaSetup->getConnection();

        try {
            if (
                $connection->isTableExists(self::MAGENEST_SOCIAL_LOGIN_ACCOUNT_TABLE)
            ) {
                $foreignKeyName = $connection->getForeignKeyName(
                    self::MAGENEST_SOCIAL_LOGIN_ACCOUNT_TABLE,
                    self::CUSTOMER_ID_COLUMN,
                    self::CUSTOMER_ENTITY_TABLE,
                    self::ENTITY_ID
                );
                $tableSocialLoginName = $connection->getTableName(self::MAGENEST_SOCIAL_LOGIN_ACCOUNT_TABLE);
                $versionMagento = $this->productMetadata->getVersion();

                // Add padding = 10 to social login account table to backward compatible
                if ($versionMagento < '2.4.0') {
                    $connection->modifyColumn(
                        $tableSocialLoginName,
                        self::CUSTOMER_ID_COLUMN,
                        [
                            'type' => Table::TYPE_INTEGER,
                            'padding' => 10,
                            'unsigned' => true,
                            'nullable' => false,
                            'comment' => 'Customer ID'
                        ]
                    );
                }

                // Add Foreign Key to magenest social login account table reference to customer entity table
                $connection->dropForeignKey(self::MAGENEST_SOCIAL_LOGIN_ACCOUNT_TABLE, $foreignKeyName);
                $connection->addForeignKey(
                    $foreignKeyName,
                    $tableSocialLoginName, /* main table name */
                    self::CUSTOMER_ID_COLUMN,
                    $connection->getTableName(self::CUSTOMER_ENTITY_TABLE),
                    self::ENTITY_ID,
                    AdapterInterface::FK_ACTION_CASCADE
                );
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        $this->schemaSetup->endSetup();

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
