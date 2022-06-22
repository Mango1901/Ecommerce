<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magenest\StoreCredit\Model\Action\ActionInterface;
use UnexpectedValueException;

/**
 * Class ActionFactory
 * @package Magenest\StoreCredit\Model
 */
class ActionFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $map;

    /**
     * Factory constructor
     *
     * @param ObjectManager $objectManager
     * @param array $map
     */

    public function __construct(
        ObjectManager $objectManager,
        array $map = []
    ) {
        $this->objectManager = $objectManager;
        $this->map = $map;
    }

    /**
     * @param $param
     * @param array $arguments
     *
     * @return mixed
     */
    public function create($param, array $arguments = [])
    {
        if (!isset($this->map[$param])) {
            throw new UnexpectedValueException(
                __('Action does not exist')
            );
        }
        $instance = $this->objectManager->create($this->map[$param], $arguments);

        if (!$instance instanceof ActionInterface) {
            throw new UnexpectedValueException(
                'Class ' . get_class($instance) . ' should be an instance of ActionInterface'
            );
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function getOptionHash()
    {
        $options = [];
        foreach ($this->map as $actionCode => $actionClass) {
            $instance = $this->objectManager->create($actionClass);
            if (!$instance instanceof ActionInterface) {
                continue;
            }
            $options[$actionCode] = $instance->getAction();
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getOptionHash() as $code => $label) {
            $options[] = ['value' => $code, 'label' => $label];
        }

        return $options;
    }
}
