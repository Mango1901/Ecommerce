<?php
namespace Magenest\SocialLogin\Block;

use Magenest\SocialLogin\Model\Share\Share as ShareModel;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Share
 * @package Magenest\SocialLogin\Block
 */
class Share extends Template
{
    /**
     * @var \Magenest\SocialLogin\Model\Share\Share
     */
    protected $_clientShare;

    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param \Magenest\SocialLogin\Model\Share\Share $clientShare
     * @param Registry $registry
     */
    public function __construct(
        Context    $context,
        ShareModel $clientShare,
        Registry   $registry
    ) {
        $this->_clientShare  = $clientShare;
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
     * @return bool
     */
    public function isShareEnabled()
    {
        return $this->_clientShare->isEnabled();
    }

    /**
     * @return array|int[]|string[]
     */
    public function getSocialShare()
    {
        return $this->_clientShare->getSocialShare();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_product->getName();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShareBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMedia()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $url .= 'catalog/product' . $this->_product->getImage();
        if ($this->_product->getImage()) {
            return $url;
        }
        return $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
    }

    /**
     * @return mixed
     */
    public function getZaloOfficialAccount()
    {
        return $this->_clientShare->getZaloOaId();
    }
}
