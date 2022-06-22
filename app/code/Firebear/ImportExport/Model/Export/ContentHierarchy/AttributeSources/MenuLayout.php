<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\VersionsCms\Model\Source\Hierarchy\Menu\Layout as HierarchyMenuLayout;

/**
 * Class MenuLayout
 *
 * @package Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources
 */
class MenuLayout extends AbstractSource
{
    /**
     * @var HierarchyMenuLayout
     */
    private $menuLayout;

    /**
     * MenuLayout constructor.
     *
     * @param array $data
     */
    public function __construct(
        array $data = []
    ) {
        $this->menuLayout = $data['menuLayout'];
    }

    /**
     * Return options for displaying Hierarchy Menu
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->menuLayout->toOptionArray();
        }

        return $this->_options;
    }
}
