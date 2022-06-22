<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 04/03/2019
 * Time: 15:41
 */

namespace Magenest\Slider\Model\ResourceModel\Slider;


use Magento\Framework\Option\ArrayInterface;

class CategorySliderList implements ArrayInterface
{
    protected $sliderFactory;

    public function __construct(\Magenest\Slider\Model\SliderFactory $sliderFactory)
    {
        $this->sliderFactory = $sliderFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        // TODO: Implement toOptionArray() method.
        $sliderModel = $this->sliderFactory->create();
        $sliderCollection = $sliderModel->getCollection()->addFieldToFilter('parent_id', 0);
        $sliders = [['value' => '', 'label' => __('-- Please Select --')]];
        foreach ($sliderCollection as $slider){
            $sliders[] = [
                'value' => $slider->getId(),
                'label' => $slider->getData('name'),
            ];
        }
        return $sliders;
    }
}