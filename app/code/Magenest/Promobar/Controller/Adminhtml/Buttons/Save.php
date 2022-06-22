<?php

namespace Magenest\Promobar\Controller\Adminhtml\Buttons;

use Magento\Backend\App\Action;

class Save extends \Magenest\Promobar\Controller\Adminhtml\Buttons
{
    protected $_buttonModel;

    public function __construct(Action\Context $context,
                                \Magenest\Promobar\Model\ButtonFactory $button
)
    {
        $this->_buttonModel = $button;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
			if(!isset($data['button_id'])){
				$existBars = $this->_buttonModel->create()
					->getCollection()
					->addFieldToFilter('title', $data['title']);
				if(count($existBars)>0){
					$this->messageManager->addError(__('Title already exist. Please use other title'));
					return $resultRedirect->setPath('*/*/');
				}
			}
            $id = $this->getRequest()->getParam('id');
            $model = $this->_buttonModel->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This item no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            // init model and set data
            $editButton = [
                'height'=>$data['range-height-button'],
                'width'=>$data['range-width-button'],
                'border'=>$data['range-border-radius'],
                'top_left'=>$data['range-border-top-left'],
                'bottom_left'=>$data['range-border-bottom-left'],
                'top_right'=>$data['range-border-top-right'],
                'bottom_right'=>$data['range-border-bottom-right']
            ];
            if(isset($data['range-padding-top'])){
                $editButton['padding_top'] = $data['range-padding-top'];
            };
            if(isset($data['range-padding-bottom'])){
                $editButton['padding_bottom'] = $data['range-padding-bottom'];
            };
            if(isset($data['range-padding-right'])){
                $editButton['padding_right'] = $data['range-padding-right'];
            };
            if(isset($data['range-padding-left'])){
                $editButton['padding_left'] = $data['range-padding-left'];
            };
            $editButton = json_encode($editButton);
            $data['edit_button']=$editButton;
            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                $this->messageManager->addSuccess(__('You saved the item.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
