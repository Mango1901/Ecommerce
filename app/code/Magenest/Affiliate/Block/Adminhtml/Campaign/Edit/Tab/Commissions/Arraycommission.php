<?php


namespace Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Registry;
use Magenest\Affiliate\Helper\Data;
use Zend_Serializer_Exception;

/**
 * Class Arraycommission
 * @package Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions
 */
class Arraycommission extends Widget implements RendererInterface
{
    const TYPE_SALE_PERCENT = 1;
    const TYPE_FIXED = 3;

    /**
     * @var string
     */
    protected $_template = 'Magenest_Affiliate::commissions/list/tier.phtml';

    /**
     * @var
     */
    protected $_element;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Data
     */
    protected $affiliateHelper;

    /**
     * Arraycommission constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        Registry $registry,
        array $data = []
    ) {
        $this->affiliateHelper = $helper;
        $this->_registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * @return array
     */
    public function getCommissionType()
    {
        return [
            ['value' => self::TYPE_SALE_PERCENT, 'label' => __('Percentage of grand total')],
            ['value' => self::TYPE_FIXED, 'label' => __('Fixed amount')],
        ];
    }

    /**
     * @param $tierName
     * @param $type
     * @param null $valueSelected
     *
     * @return string
     */
    public function getCommissionTypeOptions($tierName, $type, $valueSelected = null)
    {
        $html = '<select name="commission[' . $tierName . '][' . $type . ']">';
        foreach ($this->getCommissionType() as $type) {
            $selected = $valueSelected == $type['value'] ? 'selected' : '';
            $html .= '<option ' . $selected . ' value="' . $type['value'] . '">' . $type['label'] . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * @return string
     * @throws Zend_Serializer_Exception
     */
    public function getBodyCommission()
    {
        $commissionData = $this->getCommissions();
        $html = '';
        $html .= ' <tbody id="' . $this->getElement()->getHtmlId() . '_container">';
        $html .= '<tr>';
        $html .= '<td><span class="tier-number">' . __('Tier 1') . '</span></td>';
        $html .= '<td>' . $this->getCommissionTypeOptions(
            'tier_1',
            'type',
            $commissionData['tier_1']['type']
        ) . '</td>';
        $html .= '<td><input
                    type="text"
                    class="validate-number validate-not-negative-number"
                    value="' . $commissionData['tier_1']['value'] . '"
                    name="commission[tier_1][value]"
                    ></td>';
        $html .= '<td>' . $this->getCommissionTypeOptions(
            'tier_1',
            'type_second',
            $commissionData['tier_1']['type_second']
        ) . '</td>';
        $html .= '<td><input
                    type="text"
                    class="validate-number validate-not-negative-number"
                    value="' . $commissionData['tier_1']['value_second'] . '"
                    name="commission[tier_1][value_second]"
                    ></td>';
        $html .= '</tr>';
        $html .= '</tbody>';

        return $html;
    }

    /**
     * @return array|mixed
     * @throws Zend_Serializer_Exception
     */
    public function getCommissions()
    {
        $commissionData = $this->getCommissionData();
        if (!$commissionData) {
            $this->addDefaultValue($commissionData);
        }

        return $commissionData;
    }

    /**
     * @param $commissionData
     */
    public function addDefaultValue(&$commissionData)
    {
        $commissionData['tier_1'] = [
            'name' => __('Tier 1'),
            'type' => 3,
            'value' => '',
            'type_second' => 3,
            'value_second' => ''
        ];
    }

    /**
     * @return string
     */
    public function addExtraHtml()
    {
        return '';
    }

    /**
     * @return string
     */
    public function addExtraTHead()
    {
        return '';
    }

    /**
     * @return array|mixed
     * @throws Zend_Serializer_Exception
     */
    public function getCommissionData()
    {
        $campaign = $this->_registry->registry('current_campaign');
        $commission = $campaign->getCommission();
        //fixbug unserialize $commission  = null for m2 v2.1
        if ($commission === null) {
            return $commission = [];
        } else {
            if (!is_array($commission)) {
                $commission = $this->affiliateHelper->unserialize($commission);
            }
        }

        return $commission;
    }

    /**
     * @param AbstractElement $element
     *
     * @return $this
     */
    public function setElement(AbstractElement $element)
    {
        $this->_element = $element;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getElement()
    {
        return $this->_element;
    }
}
