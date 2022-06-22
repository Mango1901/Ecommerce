<?php

namespace Magenest\FastErp\Model\Block\Source;

use Magenest\FastErp\Model\ErpHistoryLog;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class TypeErp
 */
class TypeErp implements OptionSourceInterface
{
    /**
     * @var ErpHistoryLog
     */
    protected $erpHistoryLog;

    /**
     * Constructor
     *
     * @param ErpHistoryLog $erpHistoryLog
     */
    public function __construct(ErpHistoryLog $erpHistoryLog)
    {
        $this->erpHistoryLog = $erpHistoryLog;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->erpHistoryLog->getAvailableTypeErp();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
