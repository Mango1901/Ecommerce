<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 28/03/2019
 * Time: 15:45
 */

namespace Magenest\Slider\Model\ResourceModel\Slider;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        // TODO: Implement getAllOptions() method.
        return [
            ['value' => 0, 'label' => 'OFF'],
            ['value' => 1, 'label' => 'ON'],
        ];
    }
}