<?php
namespace Magenest\MobileApi\Model\Data;

use Magenest\MobileApi\Api\Data\CustomerInterface;

class Customer extends \Magento\Customer\Model\Data\Customer implements CustomerInterface
{
    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->_get(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }
}
