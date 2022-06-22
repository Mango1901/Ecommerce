<?php
/**
 * Created by PhpStorm.
 * User: ninhvu
 * Date: 19/01/2018
 * Time: 09:27
 */

namespace Magenest\MapList\Block\Adminhtml\Brand;

use Magenest\MapList\Model\Config\Source\Router;
use Magento\Backend\Block\Template;

/**
 * Class Validate
 * @package Magenest\MapList\Block\Adminhtml\Brand
 */
class Validate extends Template
{
    /**
     * @var \Magenest\MapList\Model\BrandFactory
     */
    protected $brandFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Validate constructor.
     * @param Template\Context $context
     * @param array $data
     * @param \Magenest\ShopByBrand\Model\BrandFactory $brandFactory
     */
    public function __construct(
        Template\Context $context,
        \Magenest\MapList\Model\BrandFactory $brandFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->brandFactory = $brandFactory;
        $this->_storeManager = $storeManager;
    }


    public function getLogo()
    {
        $id = $this->getRequest()->getParam('id');
        $brand = $this->brandFactory->create()->load($id);
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($brand->getLogo() == '') {
            return null;
        }
        return $mediaUrl . Router::ROUTER_MEDIA . $brand->getLogo();
    }

}
