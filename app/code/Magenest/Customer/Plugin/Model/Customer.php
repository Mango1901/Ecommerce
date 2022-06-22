<?php

namespace Magenest\Customer\Plugin\Model;

use Magento\Eav\Model\Config;

class Customer
{
    /**
     * @var Config
     */
    protected $_config;

    public function __construct(Config $config)
    {
        $this->_config = $config;
    }

    /**
     * @param \Magento\Customer\Model\Customer $subject
     * @param string $result
     * @return string
     */
    public function afterGetName(\Magento\Customer\Model\Customer $subject, string $result): string
    {
        $name = '';

        if ($this->_config->getAttribute('customer', 'prefix')->getIsVisible() && $subject->getPrefix()) {
            $name .= $subject->getPrefix() . ' ';
        }

        $name .= $subject->getLastname();

        if ($this->_config->getAttribute('customer', 'middlename')->getIsVisible() && $subject->getMiddlename()) {
            $name .= ' ' . $subject->getMiddlename();
        }

        $name .= ' ' . $subject->getFirstname();

        if ($this->_config->getAttribute('customer', 'suffix')->getIsVisible() && $subject->getSuffix()) {
            $name .= ' ' . $subject->getSuffix();
        }

        return $name;
    }
}
