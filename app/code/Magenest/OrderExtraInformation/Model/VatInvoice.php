<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Model;

use Magento\Framework\DataObject;
use Magenest\OrderExtraInformation\Api\Data\VatInvoiceInterface;

/**
 * Class VatInvoice
 * @package Magenest\OrderExtraInformation\Model
 */
class VatInvoice extends DataObject implements VatInvoiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSaveVatInvoice()
    {
        return $this->getData(self::SAVE_VAT_INVOICE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyAddress()
    {
        return $this->getData(self::COMPANY_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyName()
    {
        return $this->getData(self::COMPANY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyEmail()
    {
        return $this->getData(self::COMPANY_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCode()
    {
        return $this->getData(self::TAX_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyName($companyName)
    {
        return $this->setData(self::COMPANY_NAME, $companyName);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyEmail($companyEmail)
    {
        return $this->setData(self::COMPANY_EMAIL, $companyEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCode($vatCode)
    {
        return $this->setData(self::TAX_CODE, $vatCode);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyAddress($companyAddress)
    {
        return $this->setData(self::COMPANY_ADDRESS, $companyAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function setSaveVatInvoice($saveVatInvoice)
    {
        return $this->setData(self::SAVE_VAT_INVOICE, $saveVatInvoice);
    }
}
