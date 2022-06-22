<?php


namespace Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\SalesRule\Helper\Coupon;

/**
 * Class CouponCodes
 * @package Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab
 */
class CouponCode extends Generic implements TabInterface
{
    /**
     * @var Coupon
     */
    private $couponHelper;

    /**
     * CouponCode constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Coupon $couponHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Coupon $couponHelper,
        array $data = []
    ) {
        $this->couponHelper = $couponHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Coupon Code');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Coupon Code');
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
        $form->setHtmlIdPrefix('coupon_');

        $fieldset = $form->addFieldset('coupon_code_fieldset', ['legend' => __('Coupon Code')]);
        $fieldset->addClass('ignore-validate');

        $fieldset->addField(
            'code_length',
            'text',
            [
                'name'     => 'code_length',
                'label'    => __('Code Length'),
                'title'    => __('Code Length'),
                'required' => true,
                'value'    => $this->couponHelper->getDefaultLength(),
                'class'    => 'validate-digits validate-greater-than-zero'
            ]
        );

        $fieldset->addField(
            'format',
            'select',
            [
                'label'    => __('Code Format'),
                'name'     => 'code_format',
                'options'  => $this->couponHelper->getFormatsList(),
                'required' => true,
                'value'    => $this->couponHelper->getDefaultFormat()
            ]
        );

        $fieldset->addField('coupon_code', 'text', [
            'name'  => 'coupon_code',
            'label' => __('Coupon Code'),
            'title' => __('Coupon Code')
        ]);

        $idPrefix    = $form->getHtmlIdPrefix();
        $generateUrl = $this->getGenerateUrl();

        $fieldset->addField(
            'generate_button',
            'note',
            [
                'text' => $this->getButtonHtml(
                    __('Generate'),
                    "generateCouponCodes('{$idPrefix}' ,'{$generateUrl}')",
                    'generate'
                )
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('affiliate/*/generate');
    }
}
