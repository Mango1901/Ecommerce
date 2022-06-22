<?php


namespace Magenest\Affiliate\Model\Total\Order\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magenest\Affiliate\Helper\Calculation;

/**
 * Class Affiliate
 * @package Magenest\Affiliate\Model\Total\Order\Creditmemo
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
     * @param Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $this->calculation->calculateAffiliateDiscount(
            $creditmemo,
            ['affiliate_discount_refunded', 'base_affiliate_discount_refunded']
        );

        return $this;
    }
}
