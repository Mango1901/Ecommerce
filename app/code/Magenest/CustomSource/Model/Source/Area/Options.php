<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomSource\Model\Source\Area;

use Magenest\Directory\Block\Adminhtml\Area\Field\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magenest\CustomSource\Helper\Data as HelperData;

/**
 * Provide option values for UI
 *
 * @api
 */
class Options implements OptionSourceInterface
{
    /**
     * Helper Data
     *
     * @var HelperData
     */
    private $helperData;

    /**
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->helperData->getAreaData() as $item) {
            $options[] = [
                'value' => $item[Area::COLUMN_AREA_CODE],
                'label' => $item[Area::COLUMN_AREA_LABEL],
            ];
        }

        return $options;
    }
}
