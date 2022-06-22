<?php

namespace Magenest\Promobar\Controller\Adminhtml\Promobars;

class Save extends \Magenest\Promobar\Controller\Adminhtml\Promobars
{

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
            if($data['multiple_content']=="" ){
                $this->messageManager->addError(__('Content is empty, Please insert content into bar'));
                return $resultRedirect->setPath('*/*/new');
            }else {
                if (!isset($data['promobar_id'])) {
                    $existBars = $this->_promobarModel->create()
                        ->getCollection()
                        ->addFieldToFilter('title', $data['title']);
                    if (count($existBars) > 0) {
//                    return $resultRedirect->setPath('*/*/new');
                        $this->messageManager->addError(__('Title already exist. Please use other Title'));
                        // redirect to edit form
                        return $resultRedirect->setPath('*/*/new');
                    }
                }
                $id = $this->getRequest()->getParam('promobar_id');
                $model = $this->_promobarModel->create()->load($id);
                $mobileModel = $this->mobilePromobarModel->create()->load($id,'promobar_id');
                $mobilePromobarId = $mobileModel->getId();
                if (!$model->getId() && $id) {
                    $this->messageManager->addError(__('This item no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
                $dataWidget = $data;

                // init model and set data
                $positionImage = [
                    'height' => $data['range-height'],
                    'width' => $data['range-width'],
                    'left-right' => $data['range-left-right'],
                    'up-down' => $data['range-up-down'],
                    'opacity' => $data['range-opacity']
                ];
                if ($data['use_same_config'] == 1){
                    $mobilePositionImage = [
                        'mobile-height' => $data['range-height'],
                        'mobile-width' => $data['range-width'],
                        'mobile-left-right' => $data['range-left-right'],
                        'mobile-up-down' => $data['range-up-down'],
                        'mobile-opacity' => $data['range-opacity'],
                    ];
                } else {
                    $mobilePositionImage = [
                        'mobile-height' => $data['mobile-range-height'],
                        'mobile-width' => $data['mobile-range-width'],
                        'mobile-left-right' => $data['mobile-range-left-right'],
                        'mobile-up-down' => $data['mobile-range-up-down'],
                        'mobile-opacity' => $data['mobile-range-opacity'],
                    ];
                }

                $data['mobile_multiple_content'] = $data['multiple_content'];

                //-------------------------------------------------------------------------------------------------------
//                $displayLeft = array_key_exists('desktop-checkbox-left', $data) ? $data['range-button-left'] . '%' : 'no check';
//                $displayRight = array_key_exists('desktop-checkbox-left', $data) ? 'no check' : $data['range-button-right'] . '%';

                    foreach ($data['multiple_content'] as $multipleContentKey => $multipleContentValue) {
                    if($data['multiple_content'][$multipleContentKey]['content'] == ''){
                        $this->messageManager->addError(__('Content is empty, Please insert content into bar'));
                        // redirect to edit form
                        return $resultRedirect->setPath('*/*/edit',
                            ['id' => $model->getId()]
                        );
                    }
                    
                    $buttonData = json_decode($multipleContentValue['button'],true);
                    if ($buttonData) {
                        if (array_key_exists('button', $buttonData)) {
                            foreach ($buttonData['button']['data'] as $key => $value) {
                                $displayLeft = $buttonData['button']['data']['displayLeft'];
                                $displayRight = $buttonData['button']['data']['displayRight'];
                                if ($key == 'displayLeft') {
                                    $buttonData['button']['data']['displayLeft'] = $displayLeft;
                                } elseif ($key == 'displayRight') {
                                    $buttonData['button']['data']['displayRight'] = $displayRight;
                                }
                            }
                            $buttonData = json_encode($buttonData);
                        }
                    }
                    $data['multiple_content'][$multipleContentKey]['button'] = $buttonData;
                }

                //-------------------------------------------------------------------------------------------------------
                $multipleContent = json_encode($data['multiple_content']);
                $editBackground = json_encode($positionImage);
                $data['edit_background'] = $editBackground;
                $data['multiple_content'] = $multipleContent;

//                if ($data['use_same_config'] == 1) {
//                    $data['mobile_edit_background'] = $data['edit_background'];
//                    $data['mobile_multiple_content'] = $data['multiple_content'];
//                    $data['mobile_height_pro_bar'] = $data['height-pro-bar'];
//                } elseif ($data['use_same_config'] == 0) {
                    if (true) {
                    $mobileEditBackground = json_encode($mobilePositionImage);
                    $data['mobile_edit_background'] = $mobileEditBackground;
                    $data['mobile_height_pro_bar'] = $data['mobile-height-pro-bar'];
                    //----------------------------------------------------------------------------------------------------------------------------
                    $data['multiple_content'] = json_decode($data['multiple_content'], true);
                    foreach ($data['mobile_multiple_content'] as $mobileMultipleContentKey => $mobileMultipleContentValue) {
                        $data['mobile_multiple_content'][$mobileMultipleContentKey]['content'] = json_decode($data['mobile_multiple_content'][$mobileMultipleContentKey]['content'], true);
                        $data['multiple_content'][$mobileMultipleContentKey]['content'] = json_decode($data['multiple_content'][$mobileMultipleContentKey]['content'], true);

                        if ($data['use_same_config'] == 1) {
                            $mobilePositionText = $data['mobile_multiple_content'][$mobileMultipleContentKey]['content']['positionText'];
                            $mobileSize = $data['mobile_multiple_content'][$mobileMultipleContentKey]['content']['size'];
                        } else{
                            $mobilePositionText = $data['mobile_multiple_content'][$mobileMultipleContentKey]['content']['mobilePositionText'];
                            $mobileSize = $data['mobile_multiple_content'][$mobileMultipleContentKey]['content']['mobileSize'];
                        }
                        $data['mobile_multiple_content'][$mobileMultipleContentKey]['content']['mobileSize'] = $mobileSize;
                        $data['mobile_multiple_content'][$mobileMultipleContentKey]['content']['mobilePositionText'] = $mobilePositionText;
                        $data['mobile_multiple_content'][$mobileMultipleContentKey]['content']['size'] = $mobileSize;
                        $data['mobile_multiple_content'][$mobileMultipleContentKey]['content']['positionText'] = $mobilePositionText . "px";
                        $data['multiple_content'][$mobileMultipleContentKey]['content']['mobile_text_size'] = $mobileSize;
                        $data['multiple_content'][$mobileMultipleContentKey]['content']['mobile_positionText'] = $mobilePositionText . "px";
                        $data['multiple_content'][$mobileMultipleContentKey]['content']['mobilePositionText'] = $mobilePositionText;

                        $mobileButtonData = json_decode($data['mobile_multiple_content'][$mobileMultipleContentKey]['button'], true);
                        if ($mobileButtonData) {
                            if (array_key_exists('button', $mobileButtonData)) {
                                if (array_key_exists('mobileDisplayLeft', $mobileButtonData['button']['data'])) {
                                    $mobileButtonData['button']['data']['displayLeft'] = $mobileButtonData['button']['data']['mobileDisplayLeft'];
                                } else {
                                    $mobileButtonData['button']['data']['displayLeft'] = $mobileButtonData['button']['data']['displayLeft'];
                                    $mobileButtonData['button']['data']['mobileDisplayLeft'] = $mobileButtonData['button']['data']['displayLeft'];
                                }

                                if (array_key_exists('mobileDisplayRight', $mobileButtonData['button']['data'])) {
                                    $mobileButtonData['button']['data']['displayRight'] = $mobileButtonData['button']['data']['mobileDisplayRight'];
                                } else {
                                    $mobileButtonData['button']['data']['displayRight'] = $mobileButtonData['button']['data']['displayRight'];
                                    $mobileButtonData['button']['data']['mobileDisplayRight'] = $mobileButtonData['button']['data']['displayRight'];
                                }
                                if (!array_key_exists('mobileUpDown', $mobileButtonData['button']['data'])) {
                                    $mobileButtonData['button']['data']['mobileUpDown'] = $mobileButtonData['button']['data']['upDown'];
                                }
                            }
                        }

                        $mobileButtonData = json_encode($mobileButtonData);
                        $data['mobile_multiple_content'][$mobileMultipleContentKey]['button'] = $mobileButtonData;
                        $data['mobile_multiple_content'][$mobileMultipleContentKey]['content'] = json_encode($data['mobile_multiple_content'][$mobileMultipleContentKey]['content']);
                        $data['multiple_content'][$mobileMultipleContentKey]['content'] = json_encode($data['multiple_content'][$mobileMultipleContentKey]['content']);

                    }

                    $data['multiple_content'] = json_encode($data['multiple_content']);
                    //----------------------------------------------------------------------------------------------------------------------------

                    $data['mobile_multiple_content'] = json_encode($data['mobile_multiple_content']);
                    if ($data['breakpoint'] == "") {
                        $data['breakpoint'] = 0;
                    }
                }

                $model->setData($data);
                if ($data['status_image'] == 0) {
                    $model->setData('background_image', "");
                }
                // try to save it
                try {
                    // save the data
//                    $model->save();
                    $this->savePromobar($model);
                    $data['promobar_id'] = $model->getId();
                    $mobileData = [
                        'mobile_promobar_id' => $mobilePromobarId,
                        'promobar_id' => $model->getId(),
                        'use_same_config' => $data['use_same_config'],
                        'breakpoint' => $data['breakpoint'],
                        'mobile_edit_background' => $data['mobile_edit_background'],
                        'mobile_height_pro_bar' => $data['mobile_height_pro_bar'],
                        'mobile_multiple_content' => $data['mobile_multiple_content'],
                    ];
                    $mobileModel->setData($mobileData);
//                    $mobileModel->save();
                    $this->savePromobarMobile($mobileModel);
                    $id = $model->getId();
                    //create widget for promo bar after save promo bar
                    if($data['widget_value'] == 1) {
                        if (!isset($data['store']) || $data['theme'] == "") {
                            $this->messageManager->addWarningMessage(__(" Can't create widget: Please choose store and theme !"));
                            if ($id) {
                                $this->getUrl('*/*/edit', ['id' => $model->getId()]);
                            } else $this->getUrl('*/*/new');
                        }

                        if (isset($data['store']) && $data['theme'] != "") {
                            $store = implode(",", $data['store']);
                            $data['store'] = $store;
                            $this->createWidget($dataWidget, $id);
                            $this->messageManager->addSuccessMessage(__("You created the widget instance"));
                            if ($id) {
                                $this->getUrl('*/*/edit', ['id' => $model->getId()]);
                            } else $this->getUrl('*/*/new');
                        }
                    }
                    // display success message
                    $this->messageManager->addSuccess(__('You saved the promobar.'));
                    // clear previously saved data from session
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                    // check if 'Save and Continue'
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                    }

                    return $resultRedirect->setPath('*/*/edit',
                        ['id' => $model->getId()]
                    );
                } catch (\Exception $e) {
                    // display error message
                    $this->messageManager->addError($e->getMessage());
                    // save data in session
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                    // redirect to edit form
                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                }
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}

