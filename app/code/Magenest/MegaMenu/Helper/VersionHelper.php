<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class VersionHelper extends AbstractHelper
{
    private $menuLogModel;

    private $menuLogResource;

    private $menuLogCollection;

    /**
     * VersionHelper constructor.
     * @param Context $context
     * @param \Magenest\MegaMenu\Model\MenuLogFactory $logFactory
     * @param \Magenest\MegaMenu\Model\ResourceModel\MenuLog $menuLogResource
     * @param \Magenest\MegaMenu\Model\ResourceModel\MenuLog\CollectionFactory $menuLogCollection
     */
    public function __construct(
        Context $context,
        \Magenest\MegaMenu\Model\MenuLogFactory $logFactory,
        \Magenest\MegaMenu\Model\ResourceModel\MenuLog $menuLogResource,
        \Magenest\MegaMenu\Model\ResourceModel\MenuLog\CollectionFactory $menuLogCollection
    ) {
        $this->menuLogCollection = $menuLogCollection;
        $this->menuLogModel = $logFactory;
        $this->menuLogResource = $menuLogResource;
        parent::__construct($context);
    }

    public function hasBackupVersion($menuId)
    {
        $logCol = $this->menuLogCollection->create()->addFieldToFilter('menu_id', $menuId);
        $versionCount = count($logCol->getItems());

        return $versionCount - 1 > 0;
    }
}