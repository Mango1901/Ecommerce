<?php

namespace Magenest\Customer\Block;

use Magento\Customer\Block\Account\SortLinkInterface;
use Magento\Framework\View\Element\Html\Link;
use Magento\Framework\View\Element\Html\Links;
use Magento\Framework\View\Element\Template\Context;
use Magenest\Affiliate\Helper\Data;

/**
 * Class Navigation
 * @package Magenest\Customer\Block
 */
abstract class AbstractNavigation extends Links implements SortLinkInterface
{
    const REGEX_URL_PATTERN = '';

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Navigation constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get links
     *
     * @return Link[]
     */
    public function getLinks()
    {
        $links = $this->_layout->getChildBlocks($this->getNameInLayout());

        usort($links, function ($a, $b) {
            return $a->getSortOrder() < $b->getSortOrder();
        });

        return $links;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $result = '<li class="nav item ' . $this->additionalClass();
        if ($this->isCurrent()) {
            $result .=  ' current';
        }
        if (empty($html)) {
            $result .= '"><a href="' . $this->getUrl($this->getPath()) . '" >'. __($this->getLabel()) .'</a>';
        } else {
            $result .= '"><a>'. __($this->getLabel()) .'</a>';
        }
        $result .= $html;
        return $result;
    }

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    protected function isCurrent()
    {
        return preg_match(static::REGEX_URL_PATTERN, trim($this->_request->getPathInfo(), '/'));
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @return string
     */
    protected function additionalClass()
    {
        return '';
    }
}
