<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\VersionsCms\Model\Source\Hierarchy\Visibility as HierarchyVisibility;

/**
 * Class Visibility
 *
 * @package Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources
 */
class Visibility extends AbstractSource
{
    /**
     * @var HierarchyVisibility
     */
    private $visibility;

    /**
     * Visibility constructor.
     *
     * @param array $data
     */
    public function __construct(
        array $data = []
    ) {
        $this->visibility = $data['visibility'];
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $options = $this->visibility->toOptionArray();
            foreach ($options as $value => $label) {
                $this->_options[] = ['label' => $label, 'value' => $value];
            }
        }

        return $this->_options;
    }
}
