<?php


namespace Magenest\Affiliate\Plugin;

use Magenest\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission as CoreArraycommission;
use Zend_Serializer_Exception;

/**
 * Class Arraycommission
 * @package Magenest\Affiliate\Plugin
 */
class Arraycommission extends CoreArraycommission
{
    /**
     * @return string
     * @throws Zend_Serializer_Exception
     */
    public function getBodyCommission()
    {
        $commissionData = $this->getCommissions();
        $html = '';
        $html .= ' <tbody id="' . $this->getElement()->getHtmlId() . '_container">';
        $tierNumber = 1;
        foreach ($commissionData as $tier => $data) {
            $html .= '<tr id="' . $tierNumber . '">';
            $html .= '<td><span class="tier-number">' . __($data['name']) . '</span></td>';
            $html .= '<td>' . $this->getCommissionTypeOptions($tier, 'type', $data['type']) . '</td>';
            $html .= '<td><input type="text" class="validate-number validate-not-negative-number" value="' . $data['value'] . '" name="commission[' . $tier . '][value]"></td>';
            $html .= '<td>' . $this->getCommissionTypeOptions($tier, 'type_second', $data['type_second']) . '</td>';
            $html .= '<td><input type="text" class="validate-number validate-not-negative-number" value="' . $data['value_second'] . '" name="commission[' . $tier . '][value_second]"></td>';
            $html .= '<td>
							<button title="' . __('Delete') . '" type="button" class="action- scalable delete icon-btn delete-commission-option">
								<span>' . __('Delete') . '</span>
							</button>
					</td>';
            $html .= '</tr>';
            $tierNumber++;
        }
        $html .= '</tbody>';

        return $html;
    }

    /**
     * @return string
     */
    public function addExtraHtml()
    {
        $html = '<button title="' . __('Add') . '" type="button" class="action- scalable add icon-btn add-commission-option" id="commission-add">
					<span>' . __('Add') . '</span>
				</button>';
        $html .= '<script type="text/x-magento-init">
            {
                "#attribute-' . $this->getElement()->getHtmlId() . '-container": {
                    "Magenest_Affiliate/js/commission":{}
                }
            }
        </script>';

        return $html;
    }

    /**
     * @return string
     */
    public function addExtraTHead()
    {
        return '<th class="col-tier-action">' . __('Action') . '</th>';
    }
}
