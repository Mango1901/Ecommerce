<?php

namespace Magenest\OrderExtraInformation\Api\Data;

interface CustomerConsignInterface
{
    /** Const */
    const SAVE_CUSTOMER_CONSIGN = 'save_customer_consign';
    const TELEPHONE = 'telephone_customer_consign';

    /**
     * Get save customer consign
     *
     * @return bool|null
     */
    public function getSaveCustomerConsign();

    /**
     * Get telephone
     *
     * @return string|null
     */
    public function getTelephoneCustomerConsign();

    /**
     * Set save customer consign
     *
     * @param bool $saveCustomerConsign
     * @return $this
     */
    public function setSaveCustomerConsign($saveCustomerConsign);

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephoneCustomerConsign($telephone);
}
