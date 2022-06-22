<?php
/**
 * Created by PhpStorm.
 * User: katsu
 * Date: 19/09/2016
 * Time: 13:35
 */

namespace Magenest\MapList\Model;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Product Status values
     */
    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;
    /**
     * Retrieve All options
     *
     * @return array
     */
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return array(self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled'));
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = array();

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = array('value' => $index, 'label' => $value);
        }

        return $result;
    }
}
