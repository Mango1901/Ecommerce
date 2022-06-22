<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MegaMenu\Block\Adminhtml\Menu\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\ProductVideo\Helper\Media;
use Magento\Framework\Json\EncoderInterface;
use Magenest\MegaMenu\Block\Adminhtml\Template\Menu as MenuContent;

/**
 * Class Menu
 * @package Magenest\MegaMenu\Block\Adminhtml\Menu\Edit\Tab
 */
class Menu extends Generic implements TabInterface
{
    /**
     * @var Media
     */
    private $mediaHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var EncoderInterface
     */
    private $jsonEncoder;

    /** Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\ProductVideo\Helper\Media $mediaHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Media $mediaHelper,
        EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->mediaHelper = $mediaHelper;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->jsonEncoder = $jsonEncoder;
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
        $form->addField('magenest_menu-entity', 'note', []);
        $fieldset = $form->addFieldset('new_magenest_menu-entity', []);

        if ($megaMenu->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'id']
            );
        }

        $fieldset->addType('menu_field', MenuContent::class);

        $fieldset->addField(
            'menu_entity',
            'menu_field',
            [
                'name' => 'menu_entity',
                'class' => 'menu_entity'
            ]
        );

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return __('Menu Setting');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('Menu Setting');
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
