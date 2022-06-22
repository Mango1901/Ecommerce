<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MegaMenu\Block\Adminhtml\Menu\Edit\Tab;

use Magento\Framework\Registry;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Store\Model\System\Store;
use Magento\ProductVideo\Helper\Media;
use Magento\Framework\Data\FormFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Backend\Block\Widget\Form\Generic;
use Magenest\MegaMenu\Model\Source\Config\Status;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magenest\MegaMenu\Model\Source\Config\EventType;
use Magenest\MegaMenu\Model\Source\Config\MobileTemplate;
use Magenest\MegaMenu\Model\Source\Config\DesktopTemplate;

/**
 * Class General
 * @package Magenest\MegaMenu\Block\Adminhtml\Menu\Edit\Tab
 */
class General extends Generic implements TabInterface
{
    protected $wysiwyg;

    protected $store;

    protected $mediaHelper;

    private $urlBuilder;

    private $jsonEncoder;

    /**
     * @var \Magenest\MegaMenu\Helper\ViewHelper
     */
    private $_viewHelper;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Media $mediaHelper
     * @param EncoderInterface $jsonEncoder
     * @param Config $wysiwyg
     * @param Store $store
     * @param \Magenest\MegaMenu\Helper\ViewHelper $viewHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Media $mediaHelper,
        EncoderInterface $jsonEncoder,
        Config $wysiwyg,
        Store $store,
        \Magenest\MegaMenu\Helper\ViewHelper $viewHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_viewHelper = $viewHelper;
        $this->mediaHelper = $mediaHelper;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->jsonEncoder = $jsonEncoder;
        $this->wysiwyg = $wysiwyg;
        $this->store = $store;
        $this->setUseContainer(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $megaMenu = $this->_coreRegistry->registry("magenest_mega_menu");
        $form = $this->_formFactory->create();
        $fieldSet = $form->addFieldset('mega_menu_form_fieldset', []);

        if ($megaMenu->getId()) {
            $fieldSet->addField('menu_id', 'hidden', [
                'name' => 'menu_id'
            ]);
            $fieldSet->addField(
                'current_version', 'note', [
                    'name' => 'current_version',
                    'label' => __('Current Version'),
                    'title' => __('Current Version'),
                    'text' => '#' . $megaMenu->getCurrentVersion()
                ]
            );
        }

        $fieldSet->addField('menu_name', 'text', [
                'name' => 'menu_name',
                'label' => __('Menu Name'),
                'title' => __('Gallery Name'),
                'required' => true
            ]
        );
        $fieldSet->addField('menu_alias', 'text', [
                'name' => 'menu_alias',
                'label' => __('Menu Alias'),
                'title' => __('Menu Alias'),
                'required' => true
            ]
        );
        $fieldSet->addField(
            'event',
            'select',
            [
                'label' => __('Event'),
                'title' => __('Event'),
                'name' => 'event',
                'options' => EventType::getAllOptions(),
            ]
        );

        $fieldSet->addField(
            'scroll_to_fixed',
            'select',
            [
                'label' => __('Scroll to Fixed'),
                'title' => __('Scroll to Fixed'),
                'name' => 'scroll_to_fixed',
                'options' => Status::getAllOptions()
            ]
        );
        $fieldSet->addField(
            'customer_group_ids',
            'multiselect',
            [
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'name' => 'customer_group_ids[]',
                'values' => $this->_viewHelper->getCustomerGroups(),
                'required' => true
            ]
        );
        $fieldSet->addField(
            'store_id',
            'multiselect',
            [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->store->getStoreValuesForForm(false, true),
            ]
        );
        $fieldSet->addField('menu_template', 'select', [
                'label' => __('Desktop Template'),
                'title' => __('Desktop Template'),
                'name' => 'menu_template',
                'required' => true,
                'options' => DesktopTemplate::getAllOptions(),
                'note' => __('Apply when width >= 768px'),
            ]
        );
        $fieldSet->addField('menu_top', 'select', [
                'label' => __('Top Position of Menu Item'),
                'title' => __('Top Position of Menu Item'),
                'name' => 'menu_top',
                'options' => ['auto' => __('Auto'), 'top' => __('Top')]
            ]
        );
        $fieldSet->addField(
            'mobile_template',
            'select',
            [
                'label' => __('Mobile Template'),
                'title' => __('Mobile Template'),
                'name' => 'mobile_template',
                'options' => MobileTemplate::getAllOptions(),
                'note' => __('Apply when width < 768px')
            ]
        );
        $fieldSet->addField(
            'disable_iblocks',
            'select',
            [
                'label' => __('Disable Item Blocks on mobile'),
                'title' => __('Disable Item Blocks on mobile'),
                'name' => 'disable_iblocks',
                'options' => Status::getAllOptions(),
                'note' => __('Item Blocks: Header Block, Left Block, Right Block, Footer Block')
            ]
        );

        $fieldSet->addField('custom_css', 'textarea', [
                'label' => __('Custom Css'),
                'title' => __('Custom Css'),
                'name' => 'custom_css'
            ]
        );
        $megaValue = $megaMenu->getData();

        if ($megaValue != null) {
            $form->setValues($megaValue);
        }

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "menu_template",
                'menu_template'
            )->addFieldMap(
                "menu_top",
                'menu_top'
            )->addFieldDependence(
                'menu_top',
                'menu_template',
                'vertical_left'
            )
        );

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return __('General Setting');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('General Setting');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }
}
