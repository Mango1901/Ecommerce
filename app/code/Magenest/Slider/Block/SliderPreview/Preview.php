<?php


namespace Magenest\Slider\Block\SliderPreview;


use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Preview extends Template implements BlockInterface
{
    protected $_template = "widget/sliderview.phtml"; // default template

    protected $sliderModelFactory;
    protected $itemModelFactory;
    protected $sliderPreviewFactory;

    public function __construct(
        Template\Context $context,
        \Magenest\Slider\Model\SliderFactory $sliderModelFactory,
        \Magenest\Slider\Model\ItemFactory $itemFactory,
        \Magenest\Slider\Model\SliderPreviewFactory $sliderPreviewFactory,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->sliderModelFactory = $sliderModelFactory;
        $this->itemModelFactory = $itemFactory;
        $this->sliderPreviewFactory = $sliderPreviewFactory;
    }

    public function getSliderData(){
        $key = $this->getRequest()->getParam('key');
        $sliderPreviewModel = $this->sliderPreviewFactory->create();

        if ($key){
            $previewCollection = $sliderPreviewModel->getCollection()->addFieldToFilter('key_id', $key);
            if (count($previewCollection->getItems())){
                $collection = $previewCollection->getFirstItem();
                $sliderData = json_decode($collection['slider_data'], true);
            }
        }

        $sliderData['id'] = $key;
        return $sliderData;
    }

    public function getChildSliderData(){
        $key = $this->getRequest()->getParam('key');
        $sliderPreviewModel = $this->sliderPreviewFactory->create();

        if ($key){
            $previewCollection = $sliderPreviewModel->getCollection()->addFieldToFilter('key_id', $key);
            if (count($previewCollection->getItems())){
                $collection = $previewCollection->getFirstItem();
                $sliderData = json_decode($collection['childSlider'], true);
            }
        }
        $sliderData['id'] = 'child' . $key;
        return $sliderData;
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
        $key = $this->getRequest()->getParam('key');
        $sliderPreviewModel = $this->sliderPreviewFactory->create();

        if ($key){
            $previewCollection = $sliderPreviewModel->getCollection()->addFieldToFilter('key_id', $key);
            if (count($previewCollection->getItems())){
                $collection = $previewCollection->getFirstItem();
                $slider = json_decode($collection['config'], true);
            }
        }
        if ($slider['type'] == '0'){
            $template = 'widget/bannerview.phtml';
        } else if ($slider['type'] == '1'){
            $template = 'widget/sliderview.phtml';
        } else {
            $template = 'widget/slider-syncing-view.phtml';
        }
        return $template;
    }

}