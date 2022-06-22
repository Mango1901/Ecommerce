<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Transaction\Edit;

/**
 * Class Form
 * @package Magenest\RewardPoints\Block\Adminhtml\Transaction\Edit
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' =>
                 [
                     'id'      => 'edit_form',
                     'action'  => $this->getData('action'),
                     'method'  => 'post',
                     'enctype' => 'multipart/form-data'
                 ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
