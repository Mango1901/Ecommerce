<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 25/03/2019
 * Time: 11:06
 */

namespace Magenest\Slider\Block\Widget;


use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class SliderView extends Template implements BlockInterface
{
    protected $_template = "widget/sliderview.phtml"; // default template

    protected $sliderModelFactory;
    protected $itemModelFactory;

    public function __construct(
        Template\Context $context,
        \Magenest\Slider\Model\SliderFactory $sliderModelFactory,
        \Magenest\Slider\Model\ItemFactory $itemFactory,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->sliderModelFactory = $sliderModelFactory;
        $this->itemModelFactory = $itemFactory;
    }

    public function getSliderId(){
        if ($this->getRequest()->getParam('previewid')){
            $slider_id = $this->getRequest()->getParam('previewid');
        }
        if ($this->getData('slider_id')){
            $slider_id = $this->getData('slider_id');
        }
        return $slider_id;
    }

    public function prepareSliderData($slider_id){

        $slider = $this->sliderModelFactory->create()->load($slider_id);

        $sliderData = json_decode($slider['data_source'], true);

        $sliderData['id'] = $slider['slider_id'];
        $sliderData['status'] = $slider['status'];
        $sliderData['type'] = $slider['type'];

        $items = $this->itemModelFactory->create()->getCollection()->addFieldToFilter('slider_id', $slider_id)->setOrder('sort_order', 'ASC');
        $itemsData = [];
        foreach ($items as $item){
            $itemsData[] = json_decode($item['data_source'], true);
        }


        $sliderData['items'] = $itemsData;

        return $sliderData;
    }

    public function getSliderData(){
        $slider_id = $this->getSliderId();
        return $this->prepareSliderData($slider_id);
    }

    public function getChildSliderData(){
        $parent_id = $this->getSliderId();
        $sliderModel = $this->sliderModelFactory->create();
        $slider = $sliderModel->getCollection()->addFieldToFilter('parent_id', $parent_id);
        return count($slider) == 0 ? null : $this->prepareSliderData($slider->getData()[0]['slider_id']);
    }

    /* Convert hexdec color string to rgb(a) string */

    public function hex2rgba($color, $opacity = false) {

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if(empty($color))
            return $default;

        //Sanitize $color if "#" is provided
        if ($color[0] == '#' ) {
            $color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
            if(abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
    }

    public function getTemplate()
    {
        $slider_id = $this->getSliderId();
        $slider = $this->sliderModelFactory->create()->load($slider_id);
        if ($slider['type'] == '0'){
            $template = 'widget/bannerview.phtml';
        } else if ($slider['type'] == '1'){
            $template = 'widget/sliderview.phtml';
        } else {
            $template = 'widget/slider-syncing-view.phtml';
        }
        return $template;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $slider_id = $this->getSliderId();
        if ($slider_id){
            $slider = $this->sliderModelFactory->create()->load($slider_id);
            if ($slider['status'] === '1') {
                return $this->fetchView($this->getTemplateFile());
            }
        }
        return;
    }
}