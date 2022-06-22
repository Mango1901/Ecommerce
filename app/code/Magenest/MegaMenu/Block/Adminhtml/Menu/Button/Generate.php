<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 13/02/2019
 * Time: 16:42
 */

namespace Magenest\MegaMenu\Block\Adminhtml\Menu\Button;

class Generate implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    protected $_urlBuilder;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
    }

    public function getButtonData()
    {
        return [
            'label' => __('Generate Sample Menu'),
            'class' => 'action',
            'on_click' => sprintf(
                'jQuery("body").trigger("processStart"); location.href = "%s"',
                $this->_urlBuilder->getUrl('*/*/generate')
            )
        ];
    }
}
