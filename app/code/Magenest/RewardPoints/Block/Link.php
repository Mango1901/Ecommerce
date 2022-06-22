<?php

namespace Magenest\RewardPoints\Block;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magenest\RewardPoints\Model\AccountFactory;


/**
 * Customer account dropdown link
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var string
     */
    protected $_template = 'Magenest_RewardPoints::link.phtml';

    /**
     * @return string
     */
    protected $_currentCustomer;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * Link constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param AccountFactory $accountFactory
     * @param CurrentCustomer $currentCustomer
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\RewardPoints\Helper\Data $helper,
        AccountFactory $accountFactory,
        CurrentCustomer $currentCustomer,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = []
    ) {
        $this->helper           = $helper;
        $this->_currentCustomer = $currentCustomer;
        $this->_accountFactory  = $accountFactory;
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('rewardpoints/customer/points');
    }

    /**
     * Render block HTML
     * @return string
     */
    protected function _toHtml()
    {
        $highlight = '';
        if ($this->getIsHighlighted()) $highlight = ' current';

        if ($this->isCurrent()) {
            $htmlContent = '<li class="nav item current">';
            $htmlContent .= '<strong>' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())) . '  (' . $this->getCurrentPoints() . ' '. $this->helper->getPointUnit() .')</strong>';
            $htmlContent .= '</li>';
        } else {
            $htmlContent = '<li class="nav item' . $highlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $htmlContent .= $this->getTitle() ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"' : '';
            $htmlContent .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) $htmlContent .= '<strong>';
            $htmlContent .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));
            if ($this->getIsHighlighted()) $htmlContent .= '</strong>';

            $htmlContent .= '<span  class="counter amount"> (' . $this->getCurrentPoints() . ' '. $this->helper->getPointUnit() .')</span>';
            $htmlContent .= '</a></li>';
        }

        return $htmlContent;
    }

    /**
     * @return int
     */
    public function getCurrentPoints()
    {
        $currentPoint = 0;
        $customerId   = $this->_currentCustomer->getCustomerId();
        $account      = $this->_accountFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->getFirstItem();
        if ($account) $currentPoint = $account->getPointsCurrent();
        if ($currentPoint == null) $currentPoint = 0;

        return $currentPoint;
    }

}
