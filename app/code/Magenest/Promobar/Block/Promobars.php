<?php

namespace Magenest\Promobar\Block;

use Magenest\Promobar\Model\ResourceModel\Promobar\Collection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Controller\ResultFactory;


/**
 * Main contact form block
 */
class Promobars extends Template
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $_promobarModel;

    protected $_buttonModel;




    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context,
                                \Magenest\Promobar\Model\PromobarFactory $promobar,
                                \Magenest\Promobar\Model\ButtonFactory $button,
                                \Magenest\Promobar\Model\ResourceModel\MobilePromobar\CollectionFactory $mobilePromobarCollectionFactory,
                                array $data = [], \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $data);
		$this->_objectManager = $objectManager;
		$this->_promobarModel = $promobar;
		$this->_buttonModel = $button;
        $this->mobilePromobarCollectionFactory = $mobilePromobarCollectionFactory;
    }

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

	public function getBarImageUrl($bar){
        if($bar->getData('background_image') != null) {
            $barUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'promobar/' . $bar->getBackgroundImage();
        }else{
            $barUrl = '';
        }
        return $barUrl;
	}

	public function getInfoEditBackground($bar){
        return json_decode($bar->getEditBackground(),true);
    }

    public function getDataPromoBar($bar){
        return json_decode($bar->getMultipleContent(),true);

    }

    public function getMobileBarById($id)
    {
        $mobilePromobarCollectionFactory = $this->mobilePromobarCollectionFactory->create();
        if (is_numeric($id)) {
            $mobileBar = $mobilePromobarCollectionFactory->addFieldToFilter('promobar_id', $id)->getFirstItem();
            if ($mobileBar->getId()) {
                return $mobileBar;
            } else {
                return;
            }
        }
    }
}

