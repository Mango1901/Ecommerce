<?php
namespace Magenest\PhotoReview\Model\Config\Source;

class Social implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = array(
            array(
                'value' => 0,
                'label' => 'Facebook'
            ),
            array(
                'value' => 1,
                'label' => 'Twitter'
            )
        );
        return $arr;
    }
}