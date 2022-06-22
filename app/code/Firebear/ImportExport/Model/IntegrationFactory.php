<?php
/**
 * @copyright: Copyright © 2018 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */


namespace Firebear\ImportExport\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class IntegrationFactory
 *
 * @package Firebear\ImportExport\Model
 */
class IntegrationFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleManager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param string $type
     * @param array $data
     * @return mixed
     */
    public function create($type, array $data = [])
    {
        if ($this->isModuleEnabled($type)) {
            return $this->objectManager->create($type, $data);
        }
        return false;
    }

    /**
     * @param $type
     * @return bool
     */
    private function isModuleEnabled($type)
    {
        $moduleName = implode('_', array_slice(explode('\\', $type), 0, 2));
        return $this->moduleManager->isEnabled($moduleName);
    }
}
