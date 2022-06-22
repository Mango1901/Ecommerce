<?php

namespace Magenest\RewardPoints\Block;

class LandingPage extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $checkLanding;

    /**
     * LandingPage constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magenest\RewardPoints\Helper\Data $checkLanding
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magenest\RewardPoints\Helper\Data $checkLanding,
        array $data = []
    )
    {
        $this->checkLanding = $checkLanding;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $defaultPath, $data);
    }

    public function getPath()
    {
        return $this->getLandingPage();
    }

    /**
     * Get landing page value from config
     *
     * @return mixed
     */
    public function getLandingPage() {
        return $this->scopeConfig->getValue('rewardpoints/point_config/landing_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getLandingPage());
    }

    public function _toHtml()
    {
        $check = $this->checkLanding->getEnableModule();
        if ($check){
            if (false != $this->getTemplate()) {
                return parent::_toHtml();
            }

            $highlight = '';

            if ($this->getIsHighlighted()) {
                $highlight = ' current';
            }

            if ($this->isCurrent()) {
                $html = '<li class="nav item current">';
                $html .= '<strong>'
                    . $this->escapeHtml(__($this->getLabel()))
                    . '</strong>';
                $html .= '</li>';
            } else {
                $html = '<li class="nav item' . $highlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '"';
                $html .= $this->getTitle()
                    ? ' title="' . $this->escapeHtml(__($this->getTitle())) . '"'
                    : '';
                $html .= $this->getAttributesHtml() . '>';

                if ($this->getIsHighlighted()) {
                    $html .= '<strong>';
                }

                $html .= $this->escapeHtml(__($this->getLabel()));

                if ($this->getIsHighlighted()) {
                    $html .= '</strong>';
                }

                $html .= '</a></li>';
            }

            return $html;
        } else {
            return null;
        }
    }
}