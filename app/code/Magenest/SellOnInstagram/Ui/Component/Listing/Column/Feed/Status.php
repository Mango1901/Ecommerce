<?php

namespace Magenest\SellOnInstagram\Ui\Component\Listing\Column\Feed;

use Magenest\SellOnInstagram\Model\InstagramFeed;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => InstagramFeed::STATUS_ENABLE, 'label' => __('Active')],
            ['value' => InstagramFeed::STATUS_DISABLE, 'label' => __('Inactive')],
        ];
    }
}
