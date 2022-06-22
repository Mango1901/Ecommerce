<?php
namespace Magenest\CustomFrontend\Plugin\Block\Address\Renderer;

use Magenest\Customer\Helper\FormatHelper;
use Magenest\CustomFrontend\Setup\Patch\Data\AddAddressAttributes;
use Magento\Customer\Block\Address\Renderer\RendererInterface as RendererInterfaceMagento;

/**
 * Class RendererInterface
 */
class RendererInterface
{
    /**
     * @var FormatHelper
     */
    private $formatHelper;

    /**
     * @param FormatHelper $formatHelper
     */
    public function __construct(FormatHelper $formatHelper)
    {
        $this->formatHelper = $formatHelper;
    }

    /**
     * @param RendererInterfaceMagento $subject
     * @param $addressAttributes
     * @param null $format
     * @return array
     */
    public function beforeRenderArray(RendererInterfaceMagento $subject, $addressAttributes, $format = null)
    {
        if ($addressAttributes && $addressAttributes['telephone'])
        {
            $addressAttributes['telephone'] = $this->formatHelper->formatTelephoneVietnamese($addressAttributes['telephone']);
        }
        return [$addressAttributes, $format];
    }
}
