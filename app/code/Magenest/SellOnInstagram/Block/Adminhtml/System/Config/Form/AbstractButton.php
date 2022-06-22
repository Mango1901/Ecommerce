<?php
namespace Magenest\SellOnInstagram\Block\Adminhtml\System\Config\Form;

use Magenest\SellOnInstagram\Helper\Helper;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magenest\SellOnInstagram\Logger\Logger;
use Magento\Framework\Url;

abstract class AbstractButton extends Field
{
    protected $_template = "system/config/page-access-token.phtml";
    protected $_buttonLabel = "Get Page Access Token";
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var WriterInterface
     */
    protected $writer;
    /**
     * @var Url
     */
    protected $frontendUrl;

    public function __construct(
        Helper $helper,
        Logger $logger,
        WriterInterface $writer,
        Url $frontendUrl,
        Context $context,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->writer = $writer;
        $this->frontendUrl = $frontendUrl;
        parent::__construct($context, $data);
    }

    /**
     * @return AbstractButton
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
     * @return string
     */
    public function getButtonLabel()
    {
        return $this->_buttonLabel;
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
}
