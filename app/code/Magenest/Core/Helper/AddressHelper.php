<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class AddressHelper extends AbstractHelper
{
    protected $directoryHelper;

    /**
     * Address constructor.
     *
     * @param Context $context
     * @param \Magenest\Directory\Helper\DirectoryHelper $helper
     */
    public function __construct(
        Context $context,
        \Magenest\Directory\Helper\DirectoryHelper $helper
    ) {
        $this->directoryHelper = $helper;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\Api\AttributeInterface|null $ward
     * @return string
     */
    public function getWardName($ward)
    {
        if ($ward && $ward->getValue()) {
            $result = $this->directoryHelper->getWardDefaultName($ward->getValue());
            return $result ? $result . "," : '';
        }

        return "";
    }

    /**
     * @param \Magento\Framework\Api\AttributeInterface|null $district
     * @return string
     */
    public function getDistrictName($district)
    {
        if ($district && $district->getValue()) {
            $result = $this->directoryHelper->getDistrictDefaultName($district->getValue());
            return $result ? $result . "," : '';
        }

        return "";
    }

    /**
     * @param \Magento\Framework\Api\AttributeInterface|null $city
     * @return string
     */
    public function getCityName($city)
    {
        if ($city && $city->getValue()) {
            $result = $this->directoryHelper->getCityDefaultName($city->getValue());
            return $result ? $result . "," : '';
        }

        return "";
    }
}
