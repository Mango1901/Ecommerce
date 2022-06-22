<?php


namespace Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab;

use IntlDateFormatter;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Magenest\Affiliate\Model\Account\Group;
use Magenest\Affiliate\Model\Campaign\Display;
use Magenest\Affiliate\Model\Campaign\Status;

/**
 * Class Campaign
 * @package Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab
 */
class Campaign extends Generic implements TabInterface
{
    /**
     * Country options
     *
     * @var Yesno
     */
    protected $_boolean;

    /**
     * Status options
     *
     * @var Status
     */
    protected $_status;

    /**
     * @type Store
     */
    protected $_store;

    /**
     * @type Group
     */
    protected $_group;

    /**
     * @type Display
     */
    protected $_display;

    /**
     * @param Yesno $boolean
     * @param Status $status
     * @param Group $group
     * @param Display $display
     * @param Store $store
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Yesno $boolean,
        Status $status,
        Group $group,
        Display $display,
        Store $store,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->_boolean = $boolean;
        $this->_status = $status;
        $this->_store = $store;
        $this->_group = $group;
        $this->_display = $display;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magenest\Affiliate\Model\Campaign $campaign */
        $campaign = $this->_coreRegistry->registry('current_campaign');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('campaign_');
        $form->setFieldNameSuffix('campaign');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Campaign Information'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
        ]);
        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
        ]);
        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'required' => true,
            'values' => $this->_status->toOptionArray()
        ]);
        $fieldset->addField('website_ids', 'multiselect', [
            'name' => 'website_ids',
            'label' => __('Website IDs'),
            'title' => __('Website IDs'),
            'required' => true,
            'values' => $this->_store->getWebsiteValuesForForm(),
        ]);
        $fieldset->addField('affiliate_group_ids', 'multiselect', [
            'name' => 'affiliate_group_ids',
            'label' => __('Affiliate Groups'),
            'title' => __('Affiliate Groups'),
            'required' => true,
            'values' => $this->_group->toOptionArray(),
        ]);
        $fieldset->addField('display', 'select', [
            'name' => 'display',
            'label' => __('Display'),
            'title' => __('Display'),
            'required' => true,
            'values' => $this->_display->toOptionArray()
        ]);
        $fieldset->addField('from_date', 'date', [
            'name' => 'from_date',
            'label' => __('Active From Date'),
            'title' => __('Active From Date'),
            'date_format' => $this->_localeDate->getDateFormat(IntlDateFormatter::MEDIUM),
            'class' => 'validate-date validate-date-range date-range-attribute-from date',
        ]);
        $fieldset->addField('to_date', 'date', [
            'name' => 'to_date',
            'label' => __('Active To Date'),
            'title' => __('Active To Date'),
            'date_format' => $this->_localeDate->getDateFormat(IntlDateFormatter::MEDIUM),
            'class' => 'validate-date validate-date validate-date-range date-range-attribute-to date',
        ]);
        $fieldset->addField('sort_order', 'text', [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
        ]);

        $form->addValues($campaign->getData());
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
        return __('Campaign Information');
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
