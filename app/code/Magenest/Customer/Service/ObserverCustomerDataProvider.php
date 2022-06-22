<?php

namespace Magenest\Customer\Service;

/**
 * Data Provider for Observer
 */
class ObserverCustomerDataProvider
{
    /**
     * @var bool
     */
    private $isIgnoreValidateTelephoneCustomer = false;

    /**
     * @param bool $value
     */
    public function setIgnoreValidateTelephoneNumber($value)
    {
        $this->isIgnoreValidateTelephoneCustomer = $value;
    }

    /**
     * Function isIgnoreValidateTelephoneCustomer
     *
     * @return bool
     */
    public function isIgnoreValidateTelephoneCustomer()
    {
        return $this->isIgnoreValidateTelephoneCustomer;
    }
}
