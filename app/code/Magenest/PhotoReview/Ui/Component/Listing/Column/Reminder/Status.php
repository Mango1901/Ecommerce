<?php
namespace Magenest\PhotoReview\Ui\Component\Listing\Column\Reminder;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magenest\PhotoReview\Model\ReminderEmail;

class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            ReminderEmail::STATUS_QUEUE => __('Queue'),
            ReminderEmail::STATUS_SENT => __('Sent'),
            ReminderEmail::STATUS_FAIL => __('Fail'),
            ReminderEmail::STATUS_CANCEL => __('Cancel'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
 