<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\VersionsCms\Model\Source\Hierarchy\Menu\Listmode as HierarchyMenuListmode;

/**
 * Class MenuListmode
 *
 * @package Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources
 */
class MenuListmode extends AbstractSource
{
    /**
     * @var HierarchyMenuListmode
     */
    private $menuListmode;

    /**
     * MenuListmode constructor.
     *
     * @param array $data
     */
    public function __construct(
        array $data = []
    ) {
        $this->menuListmode = $data['menuListmode'];
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $options = $this->menuListmode->toOptionArray();
            foreach ($options as $value => $label) {
                $this->_options[] = ['label' => $label, 'value' => $value];
            }
        }

        return $this->_options;
    }
}
