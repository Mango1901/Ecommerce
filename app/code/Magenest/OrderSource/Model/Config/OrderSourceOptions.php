<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 17/02/2021
 * Time: 13:39
 */

namespace Magenest\OrderSource\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Serialize\SerializerInterface;

class OrderSourceOptions implements OptionSourceInterface
{
    protected $scopeConfig;

    protected $serializer;

    protected $options = [];

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    public function toOptionArray()
    {
        if (!$this->options) {
            try {
                $this->options = $this->serializer->unserialize($this->scopeConfig->getValue('order_source/general/options'));
            } catch (\InvalidArgumentException $exception) {
                $this->options = [];
            }
        }
        return $this->options;
    }
}
