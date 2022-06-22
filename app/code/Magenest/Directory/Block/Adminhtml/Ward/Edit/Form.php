<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Block\Adminhtml\Ward\Edit;

use Magenest\Directory\Helper\DirectoryHelper;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magenest\Directory\Model\ResourceModel\District\CollectionFactory;

/**
 * Class Form
 *
 * @package Magenest\Directory\Block\Adminhtml\Ward\Edit
 */
class Form extends Generic
{
    /**
     * @var CollectionFactory
     */
    protected $_districtCollectionFactory;
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CollectionFactory $districtCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CollectionFactory $districtCollectionFactory,
        DirectoryHelper $directoryHelper,
        array $data = []
    ) {
        $this->_districtCollectionFactory = $districtCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->directoryHelper = $directoryHelper;
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

        $ward = $this->retrieveModel();
        $dashboard = $form->addFieldset('base_fieldset', ['legend' => __('Ward Information')]);

        if ($ward->getId()) {
            $dashboard->addField('ward_id', 'hidden', ['name' => 'id']);
        }
        $disabled = !empty($ward->getId());

        $dashboard->addField(
            'district_id', 'select', [
            'name' => 'district_id',
            'label' => __('District'),
            'title' => __('District'),
            'values' => $this->_districtCollectionFactory->create()->toOptionArray(),
            'required' => true,
            'disabled' => $disabled
        ]);
        if ($ward->getId() && $ward->getDistrictId()) {
            $dashboard->addField(
                'is_district_disabled',
                'select',
                [
                    'name' => 'is_district_disabled',
                    'label' => __('Is District Disabled'),
                    'title' => __('Is District Disabled'),
                    'values' => $this->directoryHelper->isDisableDistrictOnStoreFront(\Magenest\Directory\Model\ResourceModel\District::MAIN_TABLE,'district_id',$ward->getDistrictId()),
                    'required' => false,
                    'disabled' => true
                ]
            );
        }

        $dashboard->addField(
            'name', 'text', [
            'name' => 'name',
            'title' => __('Name'),
            'label' => __('Name'),
            'required' => true
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

        $form->setValues($ward->getData());
        $this->setForm($form);
    }

    /**
     * @return mixed
     */
    public function retrieveModel()
    {
        return $this->_coreRegistry->registry('current_ward');
    }
}
