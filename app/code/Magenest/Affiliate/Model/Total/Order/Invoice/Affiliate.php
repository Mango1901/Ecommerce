<?php


namespace Magenest\Affiliate\Model\Total\Order\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magenest\Affiliate\Helper\Calculation;

/**
 * Class Affiliate
 * @package Magenest\Affiliate\Model\Total\Order\Invoice
 */
class Affiliate extends AbstractTotal
{
    /**
     * @var Calculation
     */
    protected $calculation;

    /**
     * Affiliate constructor.
     *
     * @param Calculation $calculation
     * @param array $data
     */
    public function __construct(
        Calculation $calculation,
        array $data = []
    ) {
        $this->calculation = $calculation;

        parent::__construct($data);
    }

    /**
     * @param Invoice $invoice
     *
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $this->calculation->calculateAffiliateDiscount(
            $invoice,
            ['affiliate_discount_invoiced', 'base_affiliate_discount_invoiced']
        );

        return $this;
    }
}
