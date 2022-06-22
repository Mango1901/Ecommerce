<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 06/12/2021
 * Time: 10:20
 */

namespace Magenest\Affiliate\CustomerData;

use Magenest\Affiliate\Helper\Data as AffiliateHelper;
use Magento\Customer\CustomerData\SectionSourceInterface;

class Affiliate implements SectionSourceInterface
{
    protected $affiliateHelper;

    public function __construct(
        AffiliateHelper $affiliateHelper
    ) {
        $this->affiliateHelper = $affiliateHelper;
    }

    public function getSectionData()
    {
        $affiliate = $this->affiliateHelper->getCurrentAffiliate();

        $result = [];

        if ($affiliate) {
            $prefix = $this->affiliateHelper->getConfigGeneral('url/prefix');
            $code = $this->affiliateHelper->getConfigGeneral('url/param') == "code" ? $affiliate->getCode() : $affiliate->getAccountId();

            $affiliateParam =  $this->affiliateHelper->getConfigGeneral('url/type') == "hash" ? "#{$prefix}{$code}" : "?{$prefix}={$code}";

            $result = [
                'param' => $affiliateParam
            ];
        }

        return $result;
    }
}
