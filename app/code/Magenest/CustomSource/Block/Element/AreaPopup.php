<?php

namespace Magenest\CustomSource\Block\Element;

use Magenest\CustomSource\Helper\Data as HelperData;
use Magenest\Directory\Block\Adminhtml\Area\Field\Area;
use Magento\Framework\View\Element\Template;
use \Magenest\CustomSource\Model\Source\Area\Options;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class AreaPopup Block
 */
class AreaPopup extends \Magento\Customer\Block\Account\Customer
{
    /**
     * @var Options
     */
    protected $areaOptions;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @param Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param Options $areaOptions
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        Options $areaOptions,
        HelperData $helperData,
        array $data = []
    ) {
        $this->areaOptions = $areaOptions;
        $this->helperData = $helperData;
        parent::__construct($context, $httpContext, $data);
    }

    /**
     * @return array
     */
    public function getAreaData()
    {
        return $this->areaOptions->toOptionArray();
    }

    /**
     * @return bool
     */
    public function getAreaPopupEnable()
    {
        return $this->helperData->isEnablePopupArea();
    }

    public function getCurrentAreaCode()
    {
        return $this->httpContext->getValue(Area::COLUMN_AREA_CODE);
    }

    public function getCurrentAreaCodeLabel()
    {
        $areaCode = $this->httpContext->getValue(Area::COLUMN_AREA_CODE);
        $options = $this->areaOptions->toOptionArray();
        foreach ($options as $option) {
            if ($option['value'] == $areaCode) {
                return $option['label'];
            }
        }
        return "N/A";
    }
}
