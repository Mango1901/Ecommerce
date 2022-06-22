<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Block\Adminhtml\City\Edit;

use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Directory\Model\Config\Source\Country as CountrySource;

/**
 * Class Form
 *
 * @package Magenest\Directory\Block\Adminhtml\City\Edit
 */
class Form extends Generic
{
    /**
     * @var CountrySource
     */
    protected $_countrySource;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CountrySource $countrySource
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CountrySource $countrySource,
        array $data = []
    ) {
        $this->_countrySource = $countrySource;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');

        $city = $this->retrieveModel();
        $dashboard = $form->addFieldset('base_fieldset', ['legend' => __('City Information')]);

        if ($city->getId()) {
            $dashboard->addField('city_id', 'hidden', ['name' => 'id']);
        }
        $disabled = !empty($city->getId());

        $dashboard->addField(
            'country_id', 'select', [
            'name' => 'country_id',
            'label' => __('Country'),
            'title' => __('Country'),
            'values' => $this->_countrySource->toOptionArray(),
            'disabled' => $disabled,
            'required' => true
        ]);

        $dashboard->addField(
            'name', 'text', [
            'name' => 'name',
            'title' => __('Name'),
            'label' => __('Name'),
            'required' => true,
        ]);

        $dashboard->addField(
            'default_name', 'text', [
            'name' => 'default_name',
            'title' => __('Full Name'),
            'label' => __('FullName'),
            'required' => true
        ]);

        $dashboard->addField(
            'code', 'text', [
            'name' => 'code',
            'title' => __('Code'),
            'label' => __('Code'),
            'required' => true,
            'disabled' => $disabled
        ]);
        $dashboard->addField(
            'disable_on_storefront', 'select', [
            'name' => 'disable_on_storefront',
            'title' => __('Disable on storefront'),
            'label' => __('Disable on storefront'),
            'required' => false,
            'options' => ['1' => __('Yes'), '0' => __('No')]
        ]);

        $form->setValues($city->getData());
        $this->setForm($form);
    }

    public function retrieveModel()
    {
        return $this->_coreRegistry->registry('current_city');
    }
}
