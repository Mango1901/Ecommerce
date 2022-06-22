<?php


namespace Magenest\Affiliate\Block;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class Totals
 * @package Magenest\Affiliate\Block
 */
class Totals extends Template
{
    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        if ($source instanceof Invoice) {
            $source = $parent->getSource()->getOrder();
        }

        if ($source->getAffiliateDiscountAmount() != 0) {
            $parent->addTotal(new DataObject([
                'code'       => 'affiliate_discount',
                'value'      => -$source->getAffiliateDiscountAmount(),
                'base_value' => -$source->getBaseAffiliateDiscountAmount(),
                'label'      => __('Affiliate Discount')
            ]));
        }

        return $this;
    }
}
