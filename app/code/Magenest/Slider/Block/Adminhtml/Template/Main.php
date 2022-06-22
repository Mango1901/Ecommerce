<?php

namespace Magenest\Slider\Block\Adminhtml\Template;


use Magento\Framework\Data\OptionSourceInterface;

class Main extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magenest\Slider\Model\SliderFactory
     */
    protected $sliderModelFactory;

    protected $itemModelFactory;

    protected $wysiwyg;

    protected $_objectManager;

    protected $formKey;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\Slider\Model\SliderFactory $sliderModelFactory,
        \Magenest\Slider\Model\ItemFactory $itemModelFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwyg,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        $this->sliderModelFactory = $sliderModelFactory;
        $this->wysiwyg = $wysiwyg;
        $this->itemModelFactory = $itemModelFactory;
        $this->formKey = $formKey;
    }

    public function getSliderId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function getChildSliderId()
    {
        $parent_id = $this->getSliderId();
        if (!$parent_id) {
            return 0;
        }
        $sliderModel = $this->sliderModelFactory->create();
        $slider = $sliderModel->getCollection()->addFieldToFilter('parent_id', $parent_id);
        return count($slider) != 1 ? 0 : $slider->getData()[0]['slider_id'];
    }

    public function getSliderData($slider_id)
    {
        $sliderModel = $this->sliderModelFactory->create();
        $slider = $sliderModel->load($slider_id);

        $sliderData = [];

        if (!empty($slider->getId())) {
            $sliderData = json_decode($slider['data_source'], true);
            $sliderData['sliderName'] = $slider['name'];
            $sliderData['status'] = $slider['status'];
            $sliderData['type'] = $slider['type'];
            $sliderData['items'] = $this->getItemsData($slider->getId());
        }

        return json_encode($sliderData);
    }

    public function getItemsData($sliderId)
    {
        $itemModel = $this->itemModelFactory->create();
        $itemsData = $itemModel->getCollection()->addFieldToFilter('slider_id', $sliderId);

        $items = [];
        foreach ($itemsData as $item) {
            $data = json_decode($item['data_source'], true);
            $data['id'] = $item['item_id'];
            $data['orderId'] = $item['order_number'];
            $items[] = $data;
        }

        return $items;
    }

    public function getCategoriesTree()
    {
        $categories = $this->_objectManager->create(
            'Magento\Catalog\Ui\Component\Product\Form\Categories\Options'
        )->toOptionArray();
        return json_encode($categories);
    }

    public function getMagentoVersion()
    {
        return $this->_objectManager->get(
            'Magento\Framework\App\ProductMetadataInterface'
        )->getVersion();
    }

    public function getWysiwygConfig()
    {
        $config = $this->wysiwyg->getConfig();
        $config->setData('add_variables', false);
        $config->setData('add_widgets', false);
        $config->setData('add_directives', false);
        $config->addData([
            'settings' => [
                'mode' => 'textarea',
                'theme_advanced_buttons1' => "bold,italic,justifyleft,justifycenter,justifyright,|,fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,|,bullist,numlist,|,code",
                'theme_advanced_buttons2' => NULL,
                'theme_advanced_buttons3' => NULL,
                'theme_advanced_buttons4' => NULL,
                'theme_advanced_statusbar_location' => NULL
            ]
        ]);
        return \Zend_Json::encode($config);
    }

    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/createPreview');
    }

    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}