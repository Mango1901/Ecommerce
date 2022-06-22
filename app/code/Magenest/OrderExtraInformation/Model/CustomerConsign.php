<?php

namespace Magenest\OrderExtraInformation\Model;

use Magento\Framework\DataObject;
use Magenest\OrderExtraInformation\Api\Data\CustomerConsignInterface;

/**
 * Class CustomerConsign
 */
class CustomerConsign extends DataObject implements CustomerConsignInterface
{
    /**
     * @inheritDoc
     */
    public function getSaveCustomerConsign()
    {
        return $this->getData(self::SAVE_CUSTOMER_CONSIGN);
    }

    /**
     * @inheritDoc
     */
    public function getTelephoneCustomerConsign()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setSaveCustomerConsign($saveCustomerConsign)
    {
        return $this->setData(self::SAVE_CUSTOMER_CONSIGN, $saveCustomerConsign);
    }

    /**
     * @inheritDoc
     */
    public function setTelephoneCustomerConsign($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }
}
