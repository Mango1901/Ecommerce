<?php
namespace Magenest\SellOnInstagram\Block\Adminhtml\System\Config;

abstract class WebConfig extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;
    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $data = []
    ) {
        $this->storeFactory = $storeFactory;
        $this->websiteFactory = $websiteFactory;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = (string)$this->webConfig();
        if (strpos($html, 'success') !== false) {
        } else {
            $html = '<strong>' . $html . '</strong>';
        }

        return $html;
    }

    abstract protected function webConfig();
}
