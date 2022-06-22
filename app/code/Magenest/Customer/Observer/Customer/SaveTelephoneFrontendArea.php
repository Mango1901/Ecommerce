<?php
namespace Magenest\Customer\Observer\Customer;

class SaveTelephoneFrontendArea extends SaveTelephoneAbstract
{
    /**
     * @inheritDoc
     */
    protected function getTelephoneFromParams()
    {
        return $this->request->getParam('telephone');
    }
}
