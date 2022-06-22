<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Block\Adminhtml\Menu;

use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;

/**
 * Class Index
 * @package Magenest\MegaMenu\Block\Adminhtml\Menu
 */
class Edit extends FormContainer
{
    /** @var Registry */
    protected $_coreRegistry;

    protected $helper;

    protected $_versionHelper;

    /**
     * Constructor.
     *
     * @param Registry $registry
     * @param Context $context
     * @param \Magenest\MegaMenu\Helper\Data $helper
     * @param \Magenest\MegaMenu\Helper\VersionHelper $versionHelper
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        \Magenest\MegaMenu\Helper\Data $helper,
        \Magenest\MegaMenu\Helper\VersionHelper $versionHelper,
        array $data
    ) {
        $this->_versionHelper = $versionHelper;
        $this->helper = $helper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_MegaMenu';
        $this->_controller = 'adminhtml_menu';
        $this->_mode = 'edit';
        parent::_construct();

        $this->buttonList->add(
            'preview',
            [
                'label' => __('Preview'),
                'class' => 'save'
            ]
        );
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
            ]
        );
        if ($this->helper->isBackupEnable() && $this->_versionHelper->hasBackupVersion($this->getObjectId())) {
            $this->addButton(
                'switch',
                [
                    'label' => __('Switch Version'),
                    'class' => 'switch-version',
                    'onclick' => 'switchVersionAction(\'' . __(
                            'Are you sure you want to do this?'
                        ) . '\', \'' . $this->getSwitchVersionUrl() . '\', {data: {id: \' ' . $this->getObjectId() . ' \'}})'
                ]
            );
        }

        $this->buttonList->remove('delete');
        $this->buttonList->remove('save');
        $this->addButton(
            'savemenu',
            [
                'label' => __('Save'),
                'class' => 'save primary'
            ],
            1
        );

    }

    public function getSwitchVersionUrl()
    {
        return $this->getUrl('menu/menu/edit', ['id' => $this->getObjectId()]);
    }

    public function getObjectId()
    {
        return (int)$this->getRequest()->getParam($this->_objectId);
    }
}
