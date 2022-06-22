<?php


namespace Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission;

/**
 * Class Commissions
 * @package Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab
 */
class Commissions extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @type Arraycommission
     */
    protected $arrayCommission;

    /**
     * Commissions constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Arraycommission $arraycommission
     * @param Fieldset $rendererFieldset
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Arraycommission $arraycommission,
        Fieldset $rendererFieldset,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->arrayCommission = $arraycommission;

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

        $renderer = $this->_rendererFieldset->setTemplate(
            'Magenest_Affiliate::commissions/list.phtml'
        );

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Pay Per Sale'),
            'class' => 'fieldset-wide'
        ])->setRenderer($renderer);

        $fieldset->addField('commissions', 'text', [
            'name' => 'commissions',
            'label' => __('Add Commission Type and Value'),
            'title' => __('Add Commission Type and Value')
        ])->setRenderer($this->arrayCommission);

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
        return __('Commissions');
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
