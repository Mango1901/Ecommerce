<?php

namespace Magenest\Promobar\Block\Widget;

use Magenest\Promobar\Model\MobilePromobarFactory;

/**
 * Cms Static Block Widget
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class MobilePromobars extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $_promobarModel;

    protected $_buttonModel;

    protected $_promobarCollection;

    protected $_elementFactory;

    protected $_template = "mobile_promobars.phtml";

    protected $mobilePromobarCollectionFactory;



    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Magenest\Promobar\Model\ResourceModel\Promobar\CollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\Promobar\Model\PromobarFactory $promobar,
        \Magenest\Promobar\Model\ResourceModel\MobilePromobar\CollectionFactory $mobilePromobarCollectionFactory,
        \Magenest\Promobar\Model\ResourceModel\Promobar\CollectionFactory $promobarCollection,
        \Magenest\Promobar\Model\ButtonFactory $button,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_promobarModel = $promobar;
        $this->_buttonModel = $button;
		$this->_objectManager = $objectManager;
        $this->_promobarCollection = $promobarCollection;
        $this->_elementFactory = $elementFactory;
        $this->mobilePromobarCollectionFactory = $mobilePromobarCollectionFactory;
        $this->setTemplate('mobile_promobars.phtml');
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */

    public function getModel(){
        return $this->_promobarModel->create();
	}

	public function getBarById($id){
		if (!is_numeric($id)) {
            $bar = $this->getModel()->getCollection()->addFieldToFilter('title', $id)->getFirstItem();
			if($bar->getId()){
				return $bar;
			}else{
				return;
			}
        }else{
			$bar = $this->getModel()->load($id);
			return $bar;
		}
	}

	public function getBarImageUrl($image){
        $barUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'promobar/'.$image;
		return $barUrl;
	}

    public function getMobileBarById($id)
    {
        $mobilePromobarCollectionFactory = $this->mobilePromobarCollectionFactory->create();
        if (is_numeric($id)) {
            $mobileBar = $mobilePromobarCollectionFactory->addFieldToFilter('promobar_id', $id)->getFirstItem();
            if($mobileBar->getId()){
                return $mobileBar;
            }else{
                return;
            }
        }
    }

}
