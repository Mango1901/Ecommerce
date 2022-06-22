<?php

namespace Magenest\Promobar\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class Buttons extends Template
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $_buttonModel;
	
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context,
                                \Magenest\Promobar\Model\ButtonFactory $button,
                                array $data = [], \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $data);
		$this->_objectManager = $objectManager;
		$this->_buttonModel = $button;
    }
	
	public function getModel(){
		return $this->_buttonModel->create();
	}
	
	public function getButtonById($id){
		if (!is_numeric($id)) {
            $button = $this->getModel()->getCollection()->addFieldToFilter('title', $id)->getFirstItem();
			if($button->getId()){
				return $button;
			}else{
				return;
			}
        }else{
            $button = $this->getModel()->load($id);
			return $button;
		}
	}
    public function getInfoEditButton($button){
        return json_decode($button->getEditButton(),true);
    }

}

