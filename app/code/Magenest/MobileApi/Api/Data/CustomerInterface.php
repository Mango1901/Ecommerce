<?php
namespace Magenest\MobileApi\Api\Data;

interface CustomerInterface extends \Magento\Customer\Api\Data\CustomerInterface
{
    const TELEPHONE = "telephone";

    /**
     * @return string
     */
    public function getTelephone();

    /**
     * @param string $telephone
     *
     * @return $this
     */
    public function setTelephone($telephone);
}
