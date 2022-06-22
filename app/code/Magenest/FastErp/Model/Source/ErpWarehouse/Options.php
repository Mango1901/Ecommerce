<?php
declare(strict_types=1);

namespace Magenest\FastErp\Model\Source\ErpWarehouse;

use Magenest\Directory\Block\Adminhtml\Area\Field\Area;
use Magenest\FastErp\Block\Adminhtml\Form\Field\ErpWarehouses;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Provide option values for UI
 *
 * @api
 */
class Options implements OptionSourceInterface
{
    const PATH_CONFIG = 'fast_erp/warehouses/erp_warehouses';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getErpWarehouese() as $item) {
            $options[] = [
                'value' => $item[ErpWarehouses::COLUMN_ERP_SOURCE_CODE],
                'label' => $item[ErpWarehouses::COLUMN_ERP_SOURCE_CODE] . " - " . $item[ErpWarehouses::COLUMN_ERP_SOURCE_NAME],
            ];
        }

        return $options;
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    private function getErpWarehouese($storeId = null)
    {
        $dataString = $this->scopeConfig->getValue(
            self::PATH_CONFIG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        try {
            return $this->serializer->unserialize($dataString);
        } catch (\Exception $exception) {
            return [];
        }
    }
}
