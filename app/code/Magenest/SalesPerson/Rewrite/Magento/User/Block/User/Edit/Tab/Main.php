<?php
/**
 * Copyright © SalesPerson All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\SalesPerson\Rewrite\Magento\User\Block\User\Edit\Tab;

class Main extends \Magento\User\Block\User\Edit\Tab\Main
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $model = $this->_coreRegistry->registry('permissions_user');
        $baseFieldset = $form->getElement('base_fieldset');
        $baseFieldset->addField(
            'is_salesperson',
            'select',
            [
                'name' => 'is_salesperson',
                'label' => __('Sales Person is'),
                'id' => 'is_salesperson',
                'title' => __('Sales Person Status'),
                'class' => 'select',
                'value' => $model->getData("is_salesperson"),
                'options' => ['1' => __('Active'), '0' => __('Inactive')]
            ]
        );
        return $this;
    }
}
