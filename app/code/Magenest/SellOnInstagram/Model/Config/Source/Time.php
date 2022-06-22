<?php

namespace Magenest\SellOnInstagram\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Time implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        for ($i = 0; $i < 24; ++$i) {
            $hour = $i;
            $suffix = ' AM';
            if ($hour > 11) {
                $hour -= 12;
                $suffix = ' PM';
            }

            if ($hour > 0 && $hour < 10) {
                $hour = '0'.$hour;
            }

            if ($hour == 0){
                $hour = 12;
            }

            $result[] = [
                'label' => $hour.':00'.$suffix,
                'value' => $i * 60,
            ];
            $result[] = [
                'label' => $hour.':30'.$suffix,
                'value' => $i * 60 + 30,
            ];
        }

        return $result;
    }
}
