<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 21/10/2021
 * Time: 11:03
 */

namespace Magenest\CustomizePdf\Block;


use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class DownloadPdf extends Template
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * DownloadPdf constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
        $this->getProduct();
        parent::__construct($context);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    /**
     * @return string
     */
    public function getDownloadableLink()
    {
        return $this->getBaseUrl(). 'pub/'. $this->_product->getProductInstructionPdf();
    }

    /**
     * @return bool
     */
    public function canShowDownloadableLink()
    {
        return !!$this->_product->getProductInstructionPdf();
    }

    /**
     * @return string|null
     */
    public function getIframeYoutubeVideoProduct()
    {
        return $this->_product->getYoutubeVideoIframe();
    }
}
