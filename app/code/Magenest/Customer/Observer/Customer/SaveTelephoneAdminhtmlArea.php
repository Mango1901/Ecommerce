<?php
namespace Magenest\Customer\Observer\Customer;

/**
 * Class SaveTelephoneAdminhtmlArea
 */
class SaveTelephoneAdminhtmlArea extends SaveTelephoneAbstract
{
    /**
     * @inheritDoc
     */
    protected function getTelephoneFromParams()
    {
        return $this->request->getParams()['customer']['telephone'] ?? null;
    }
}
