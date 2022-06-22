<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 24/05/2019
 * Time: 17:06
 */
// @codingStandardsIgnoreFile

namespace Magenest\Core\Model;

class Serializer{

    public function __construct(
        \Magento\Framework\Unserialize\Unserialize $unserial,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface
    ){
        $this->unserial = $unserial;
        if (interface_exists(\Magento\Framework\Serialize\SerializerInterface::class)) {
            $this->serialize = $objectManagerInterface->get(\Magento\Framework\Serialize\SerializerInterface::class);
        }

    }

    private $serialize;
    private $unserial;

    public function serialize($val) {
        if ($this->serialize === null) {
            return serialize($val);
        }
        return $this->serialize->serialize($val);
    }

    public function unserialize($val) {
        if ($this->serialize === null) {
            return $this->unserial->unserialize($val);
        }
        try {
            return $this->serialize->unserialize($val);
        } catch (\InvalidArgumentException $e) {
            return unserialize($val);
        }
    }
}
