<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 28/03/2019
 * Time: 08:54
 */

namespace Magenest\Slider\Ui\DataProvider\Slider;


class SliderDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $sliderFactory;

    public function __construct(
        $name, $primaryFieldName, $requestFieldName,
        \Magenest\Slider\Model\SliderFactory $sliderFactory,
        array $meta = [], array $data = [])
    {
        $this->collection = $sliderFactory->create()->getCollection()->addFieldToFilter('parent_id', 0);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}