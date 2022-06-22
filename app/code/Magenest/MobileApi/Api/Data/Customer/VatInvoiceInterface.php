<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Customer;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;
use Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface;

/**
 * Interface VatInvoiceInterface
 * @package Magenest\MobileApi\Api\Data\Customer
 */
interface VatInvoiceInterface extends ExtensibleDataInterface
{
    /**
     * Get Company Name
     *
     * @return string
     * @since 102.0.0
     */
    public function getCompanyName();

    /**
     * Set Company Name
     *
     * @param string $companyName
     * @return $this
     */
    public function setCompanyName($companyName);

    /**
     * Get Tax Code
     *
     * @return string
     * @since 102.0.0
     */
    public function getTaxCode();

    /**
     * Set Tax Code
     *
     * @param string $taxCode
     * @return $this
     */
    public function setTaxCode($taxCode);

    /**
     * Get Company Name
     *
     * @return string
     * @since 102.0.0
     */
    public function getCompanyAddress();

    /**
     * Set Company Address
     *
     * @param string $companyAddress
     * @return $this
     */
    public function setCompanyAddress($companyAddress);
}
