<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\VersionsCms\Model\Source\Hierarchy\Menu\Listtype as HierarchyMenuListtype;

/**
 * Class MenuListtype
 *
 * @package Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources
 */
class MenuListtype extends AbstractSource
{
    /**
     * @var HierarchyMenuListtype
     */
    private $menuListtype;

    /**
     * MenuListtype constructor.
     *
     * @param array $data
     */
    public function __construct(
        array $data = []
    ) {
        $this->menuListtype = $data['menuListtype'];
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $options = $this->menuListtype->toOptionArray();
            foreach ($options as $value => $label) {
                $this->_options[] = ['label' => $label, 'value' => $value];
            }
        }

        return $this->_options;
    }
}
