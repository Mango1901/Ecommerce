<?php

namespace Magenest\RewardPoints\Block;

use Magento\Customer\Helper\Session\CurrentCustomer;


/**
 * Customer account dropdown link
 */
class Referral extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @return string
     */
    protected $_currentCustomer;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $referHelper;

    /**
     * @var null|\Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magenest\RewardPoints\Model\AccountFactory
     */
    protected $accountFactory;

    /**
     * Referral constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CurrentCustomer $currentCustomer
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magenest\RewardPoints\Helper\Data $referHelper
     * @param \Magenest\RewardPoints\Model\AccountFactory $accountFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CurrentCustomer $currentCustomer,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magenest\RewardPoints\Helper\Data $referHelper,
        \Magenest\RewardPoints\Model\AccountFactory $accountFactory,
        array $data = []
    ) {
        $this->_currentCustomer = $currentCustomer;
        $this->referHelper = $referHelper;
        $this->accountFactory = $accountFactory;
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('rewardpoints/customer/referral');
    }

    /**
     * Render block HTML
     * @return string
     */
    protected function _toHtml()
    {

        $highlight = '';

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current">';
            $html .= '<strong>'
                . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()))
                . '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $highlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }

            $html .= '</a></li>';
        }

        return $html;
    }
}
