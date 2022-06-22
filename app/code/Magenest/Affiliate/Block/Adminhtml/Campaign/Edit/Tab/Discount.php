<?php


namespace Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Actions;
use Magenest\Affiliate\Model\Campaign\Discount as CampaignDiscount;

/**
 * Class Discount
 * @package Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab
 */
class Discount extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var Actions
     */
    protected $_ruleActions;

    /**
     * @var Yesno
     */
    protected $_boolean;

    /**
     * @var CampaignDiscount
     */
    protected $_discount;

    /**
     * Discount constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $boolean
     * @param Actions $ruleActions
     * @param CampaignDiscount $discount
     * @param Fieldset $rendererFieldset
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $boolean,
        Actions $ruleActions,
        CampaignDiscount $discount,
        Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_ruleActions = $ruleActions;
        $this->_boolean = $boolean;
        $this->_discount = $discount;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Discounts');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Discounts');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_campaign_rule');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Discount')]);

        $fieldset->addField('discount_action', 'select', [
            'name' => 'discount_action',
            'label' => __('Apply'),
            'title' => __('Apply'),
            'values' => $this->_discount->toOptionArray()
        ]);
        $fieldset->addField('discount_amount', 'text', [
            'name' => 'discount_amount',
            'label' => __('Discount Amount'),
            'title' => __('Discount Amount'),
            'class' => 'validate-number validate-not-negative-number',
            'value' => '0'
        ]);
        $fieldset->addField('apply_to_shipping', 'select', [
            'name' => 'apply_to_shipping',
            'label' => __('Apply to Shipping Amount'),
            'title' => __('Apply to Shipping Amount'),
            'values' => $this->_boolean->toOptionArray(),
        ]);
        $fieldset->addField('apply_discount_on_tax', 'select', [
            'name' => 'apply_discount_on_tax',
            'label' => __('Apply Discount On Tax'),
            'title' => __('Apply Discount On Tax'),
            'values' => $this->_boolean->toOptionArray(),
        ]);
        $fieldset->addField('discount_description', 'textarea', [
            'name' => 'discount_description',
            'label' => __('Discount Description'),
            'title' => __('Discount Description'),
        ]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
