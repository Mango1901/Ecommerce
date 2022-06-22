<?php

namespace Magenest\NotificationBox\Ui\Component\Report;

use Magento\Framework\Data\OptionSourceInterface;
use Magenest\NotificationBox\Model\CustomerToken;

/**
 * Class BooleanFilter
 */
class SubscriberStatus implements OptionSourceInterface
{
    public static function getOptionArray()
    {
        return [
            CustomerToken::STATUS_SUBSCRIBED=> __(CustomerToken::STATUS_SUBSCRIBED_LABEL),
            CustomerToken::STATUS_UNSUBSCRIBED => __(CustomerToken::STATUS_UNSUBSCRIBED_LABEL)
        ];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }
}
