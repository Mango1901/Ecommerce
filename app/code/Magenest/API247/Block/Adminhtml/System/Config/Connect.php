<?php
namespace Magenest\API247\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Connect extends Field
{
    /** @var string  */
    protected $_template = "Magenest_API247::system/config/connect.phtml";

    /**
     * @return Field
     */
    protected function _prepareLayout()
    {
        if (!$this->getTemplate()) {
            $this->setTemplate($this->_template);
        }
        return parent::_prepareLayout();
    }

    /**
     * Unset some non-related element parameters
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * create element for authorization button in store configuration
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setData([
            'html_id' => $element->getHtmlId()
        ]);

        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getConnectUrl()
    {
        return $this->_urlBuilder->getUrl("api247/config/connect");
    }
}
