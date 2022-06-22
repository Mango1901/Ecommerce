<?php


namespace Magenest\Affiliate\Block\Adminhtml\Group\Create\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magenest\Affiliate\Model\Group\Status;

/**
 * Class Group
 * @package Magenest\Affiliate\Block\Adminhtml\Group\Create\Tab
 */
class Group extends Generic implements TabInterface
{
    /**
     * Status options
     *
     * @var Status
     */
    protected $_status;

    /**
     * Group constructor.
     *
     * @param Status $status
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Status $status,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->_status = $status;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magenest\Affiliate\Model\Group $group */
        $group = $this->_coreRegistry->registry('current_group');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('group_');
        $form->setFieldNameSuffix('group');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Group Information'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
        ]);

        $form->addValues($group->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Group');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
