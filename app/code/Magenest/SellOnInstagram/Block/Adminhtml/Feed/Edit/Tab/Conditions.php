<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Serialize\Serializer\Json;
use Magenest\SellOnInstagram\Model\InstagramFeed;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Framework\Exception\LocalizedException;
use Magento\Rule\Block\Conditions as BlockConditions;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;


class Conditions extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var BlockConditions
     */
    protected $_conditions;

    /**
     * @var Json
     */
    protected $_jsonFramework;

    protected $_feedModel = null;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * Conditions constructor.
     *
     * @param Fieldset $rendererFieldset
     * @param BlockConditions $conditions
     * @param RuleFactory $ruleFactory
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Json $jsonFramework
     * @param array $data
     */
    public function __construct(
        Fieldset $rendererFieldset,
        BlockConditions $conditions,
        RuleFactory $ruleFactory,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Json $jsonFramework,
        array $data = []
    )
    {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->_ruleFactory = $ruleFactory;
        $this->_jsonFramework = $jsonFramework;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __("Conditions");
    }

    /**
     * @return Phrase|string
     */
    public function getTabTitle()
    {
        return __("Conditions");
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return $this->getFeedModel()->getId();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @return Conditions
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry(InstagramFeed::REGISTER_SALE_RULE);
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param        $model
     * @param string $fieldsetId
     * @param string $formName
     *
     * @return Form
     * @throws LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'conditions_fieldset', $formName = 'instagram_feed_form')
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $this->setForm($form);
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'catalog_rule/promo_catalog/newConditionHtml/form/' . $conditionsFieldSetId, ['form_namespace' => $formName]
        );

        $renderer = $this->_rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId);

        $fieldset = $form->addFieldset(
            $fieldsetId, ['legend' => __('Conditions (don\'t add conditions if you want to include all products in the feed)')]
        )->setRenderer($renderer);

        $fieldset->addField(
            'conditions_serialized', 'text', [
                'name' => 'conditions_serialized',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )->setRule($model)->setRenderer($this->_conditions);
        if ($this->getRequest()->getParam('id')) {
            $feedModel = $this->getFeedModel();
            if ($feedModel->getConditionsSerialized()) {
                $feedModel->setConditionsSerialized($this->_jsonFramework->unserialize($feedModel->getConditionsSerialized()));
            }
            $feedModel->setId($this->getRequest()->getParam('id'));
            $form->setValues($feedModel->getData());
        }
        $this->setConditionFormName($model->getConditions(), $formName, $conditionsFieldSetId);

        return $form;
    }

    public function getFeedModel()
    {
        if ($this->_feedModel == null) {
            $this->_feedModel = $this->_coreRegistry->registry(InstagramFeed::REGISTER);
        }

        return $this->_feedModel;
    }

    /**
     * @param AbstractCondition $conditions
     * @param                   $formName
     * @param                   $jsFormName
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName, $jsFormName)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
    }
}
