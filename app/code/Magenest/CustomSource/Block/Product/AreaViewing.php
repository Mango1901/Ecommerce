<?php

namespace Magenest\CustomSource\Block\Product;

use Magenest\CustomSource\Helper\Data;
use Magenest\CustomSource\Model\Source\Area\Options;
use Magenest\CustomSource\Plugin\SetAreaCodeContext;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Stdlib\ArrayUtils;

/**
 * Class AreaViewing
 */
class AreaViewing extends AbstractView implements IdentityInterface
{
    /**
     * @var Options
     */
    private $area;

    /**
     * @var Data
     */
    private $sourceData;

    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        Data $sourceData,
        Options $options,
        array $data = []
    ) {
        $this->area = $options;
        $this->sourceData = $sourceData;
        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * Display area viewing of product detail page
     *
     * @return bool|string
     */
    public function getCurrentArea()
    {
        $areaCode = $this->sourceData->getCurrentArea();
        if ($areaCode) {
            $areas = $this->area->toOptionArray();
            foreach ($areas as $area) {
                if ($areaCode === $area['value']) {
                    return $area['label'];
                }
            }
        }
        return false;
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return $this->getProduct()->getIdentities();
    }
}
