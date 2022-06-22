<?php

namespace Magenest\CustomTableRate\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DataDefaultForKangarooTableRate
 */
class DataDefaultForKangarooTableRate implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        try {
            $setup = $this->moduleDataSetup;
            $select = $setup->getConnection()->select()
                ->from(
                    ['city' => $setup->getTable('directory_city_entity')],
                    [
                        'city_code' => 'city.code',
                        'city_id' => 'city.city_id',
                        'country_code' => 'city.country_id',
                    ]
                )
                ->join(
                    ['district' => $setup->getTable('directory_district_entity')],
                    'district.city_id = city.city_id',
                    [
                        'district_code' => 'district.code',
                        'district_id' => 'district.district_id',
                    ]
                )
                ->where('city.country_id = ?  ', 'VN');

            $cityWithDistrictArray = $setup->getConnection()->fetchAll($select);

            $dataInsert = array_map(function($data) {
                $data['website_id'] = 1;
                $data['source_code'] = 'default';
                $data['weight'] = 1;
                $data['fee'] = 30000;
                return $data;
            }, $cityWithDistrictArray);

            $this->moduleDataSetup->getConnection()->insertArray(
                $this->moduleDataSetup->getTable('kangaroo_shipping_tablerate'),
                ['city_code', 'city_id', 'country_code', 'district_code', 'district_id', 'website_id', 'source_code', 'weight', 'fee'],
                $dataInsert
            );
        } catch (\Exception $exception) {
            $this->logger->error($exception);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
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
