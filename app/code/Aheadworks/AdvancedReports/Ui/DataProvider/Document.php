<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\DataProvider;

use Magento\Framework\Api\AttributeValueFactory;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;

/**
 * Class Document
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
class Document extends \Magento\Framework\View\Element\UiComponent\DataProvider\Document
{
    /**
     * @var PeriodModel
     */
    private $periodModel;

    /**
     * @param AttributeValueFactory $attributeValueFactory
     * @param PeriodModel $periodModel
     */
    public function __construct(
        AttributeValueFactory $attributeValueFactory,
        PeriodModel $periodModel
    ) {
        parent::__construct($attributeValueFactory);
        $this->periodModel = $periodModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttribute($attributeCode)
    {
        if ($attributeCode == 'period') {
            $period = $this->periodModel->getPeriod($this->getData());
            $this->setCustomAttribute($attributeCode, $period['period_label']);
        }

        // For Payment Type report
        if ($attributeCode == 'method') {
            $item = $this->getData();
            $methodName = $item['method'];
            if (isset($item['additional_info']) && $item['additional_info']) {
                $additionalInfo = unserialize($item['additional_info']);
                if (isset($additionalInfo['method_title'])) {
                    $methodName = $additionalInfo['method_title'];
                }
            }
            $this->setCustomAttribute($attributeCode, $methodName . ' (' . $item['method'] . ')');
        }
        return parent::getCustomAttribute($attributeCode);
    }
}
