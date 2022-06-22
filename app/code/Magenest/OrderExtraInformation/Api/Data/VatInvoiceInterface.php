<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Api\Data;

/**
 * Interface VatInvoiceInterface
 * @package Magenest\OrderExtraInformation\Api\Data
 */
interface VatInvoiceInterface
{
    /** Const */
    const SAVE_VAT_INVOICE = 'save_vat_invoice';
    const COMPANY_NAME = 'company_name';
    const COMPANY_EMAIL = 'company_email';
    const TAX_CODE = 'tax_code';
    const COMPANY_ADDRESS = 'company_address';

    /**
     * Get save vat invoice
     *
     * @return bool|null
     */
    public function getSaveVatInvoice();

    /**
     * Get company name
     *
     * @return string|null
     */
    public function getCompanyName();

    /**
     * Get company email
     *
     * @return string|null
     */
    public function getCompanyEmail();

    /**
     * Get tax code
     *
     * @return string|null
     */
    public function getTaxCode();

    /**
     * Get company address
     *
     * @return string|null
     */
    public function getCompanyAddress();

    /**
     * Set save vat invoice
     *
     * @param string $saveVatInvoice
     * @return $this
     */
    public function setSaveVatInvoice($saveVatInvoice);

    /**
     * Set company name
     *
     * @param string $companyName
     * @return $this
     */
    public function setCompanyName($companyName);

    /**
     * Set company name
     *
     * @param string $companyEmail
     * @return $this
     */
    public function setCompanyEmail($companyEmail);

    /**
     * Set tax code
     *
     * @param string $taxCode
     * @return $this
     */
    public function setTaxCode($taxCode);

    /**
     * Set company address
     *
     * @param string $companyAddress
     * @return $this
     */
    public function setCompanyAddress($companyAddress);
}
