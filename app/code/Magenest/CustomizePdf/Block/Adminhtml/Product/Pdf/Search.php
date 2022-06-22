<?php
namespace Magenest\CustomizePdf\Block\Adminhtml\Product\Pdf;

use Magento\Framework\View\Element\Template;

/**
 * Class Search
 * @package Magenest\CustomizePdf\Block\Adminhtml\Product\Pdf
 */
class Search extends Template
{
    /**
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->_urlBuilder->getUrl('magenest/pdf/search');
    }
}
