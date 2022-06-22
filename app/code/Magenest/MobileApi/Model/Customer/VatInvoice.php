<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Customer;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Customer\VatInvoiceInterface;

/**
 * Class VatInvoice
 * @package Magenest\MobileApi\Model\Catalog
 */
class VatInvoice extends AbstractSimpleObject implements VatInvoiceInterface
{
    /** @const */
    const KEY_COMPANY_NAME = 'company_name';
    const KEY_TAX_CODE = 'tax_code';
    const KEY_COMPANY_ADDRESS = 'company_address';

    /**
     * {@inheritdoc}
     */
    public function getCompanyName()
    {
        return $this->_get(self::KEY_COMPANY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCode()
    {
        return $this->_get(self::KEY_TAX_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyAddress()
    {
        return $this->_get(self::KEY_COMPANY_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyAddress($companyAddress)
    {
        return $this->setData(self::KEY_COMPANY_ADDRESS, $companyAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCode($taxCode)
    {
        return $this->setData(self::KEY_TAX_CODE, $taxCode);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyName($companyName)
    {
        return $this->setData(self::KEY_COMPANY_NAME, $companyName);
    }
}
