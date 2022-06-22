<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 22/02/2019
 * Time: 16:09
 */

namespace Magenest\Slider\Controller\Adminhtml\Slider;


use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;

class Save extends \Magenest\Slider\Controller\Adminhtml\Slider
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magenest\Slider\Model\SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var \Magenest\Slider\Model\ItemFactory
     */
    protected $itemFactory;

    protected $_storeManager;

    /**
     * @var CacheTypeListInterface
     */
    protected $cache;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    public function __construct(
        Action\Context $context,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magenest\Slider\Model\SliderFactory $sliderFactory,
        \Magenest\Slider\Model\ItemFactory $itemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CacheTypeListInterface $cache,
        CacheManager $cacheManager
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->sliderFactory = $sliderFactory;
        $this->itemFactory = $itemFactory;
        $this->cache = $cache;
        $this->cacheManager = $cacheManager;
        parent::__construct($context, $widgetFactory, $logger);
    }

    /**
     * {@inheritdoc}
     */
//    protected function _isAllowed()
//    {
//        return $this->_authorization->isAllowed();
//    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        // TODO: Implement execute() method.
        $isPost = $this->getRequest()->getPost();

        if ($isPost) {
            $position = [
                'widget_id' => $this->getRequest()->getParam('widget_id'),
                'sort_order' => $this->getRequest()->getParam('sort_order'),
                'theme' => $this->getRequest()->getParam('theme'),
                'store' => $this->getRequest()->getParam('store'),
                'pages_display' => $this->getRequest()->getParam('pages_display'),
                'container_display' => $this->getRequest()->getParam('container_display'),
            ];
            $sliderId = $this->getRequest()->getParam('slider_id');
            $sliderConfig = $this->getRequest()->getParam('sliderConfig');
            $childSliderId = $this->getRequest()->getParam('childSliderId');
            $slider = $this->getRequest()->getParam('slider');
            $childSlider = $this->getRequest()->getParam('childSlider');

            try {
                $sliderId = $this->saveSliderSettings($sliderId, json_decode($sliderConfig, true), json_decode($slider, true), $position, 0);
                $this->saveItemSettings($sliderId, json_decode($slider, true)['items']);

                if (json_decode($sliderConfig, true)['type'] == '2'){
                    $childSliderId = $this->saveSliderSettings($childSliderId, json_decode($sliderConfig, true), json_decode($childSlider, true), null, $sliderId);
                    $this->saveItemSettings($childSliderId, json_decode($childSlider, true)['items']);
                }

                $this->cache->invalidate(['layout', 'block_html', 'full_page']);
                // Display success message
                $this->messageManager->addSuccess(__('The slider has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit', ['id' => $sliderId, '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $formDataRequest = $this->getRequest()->getParams();
            $this->_getSession()->setFormData($formDataRequest);
            $this->_redirect('*/*/edit', ['id' => $sliderId]);
        }
    }

    public function saveSliderSettings($sliderId, $sliderConfig, $sliderForm, $postion = null, $parent_id){
        $sliderModel = $this->sliderFactory->create();
        if ($sliderId) {
            $sliderModel->load($sliderId);
            $sliderData['slider_id'] = $sliderId;
            if (!$sliderModel->getId()) {
                $this->messageManager->addError(__('This post no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }



        $data_not_save = [
            'items',
            'currentItem',
        ];
        foreach ($sliderForm as $key => $value){
            if (!in_array($key, $data_not_save)){
                $dataSource[$key] = $sliderForm[$key];
            }
        }
        $sliderData['name'] = $sliderConfig['sliderName'];
        $sliderData['status'] = $sliderConfig['status'];
        $sliderData['type'] = $sliderConfig['type'];
        $sliderData['data_source'] = json_encode($dataSource);
        $sliderData['parent_id'] = $parent_id;

        $sliderModel->setData($sliderData);

        // Save slider
        $sliderModel->save();

        if ($postion !== null){
            $postion['title'] = $sliderConfig['sliderName'];
            $widget_id = $this->createWidget($postion, $sliderModel->getId());
            $postion['widget_id'] = $widget_id;

            $sliderModel->setData('position', json_encode($postion))->save();
        }

        return $sliderModel->getId();
    }

    public function saveItemSettings($sliderId, $items){
        $itemModel = $this->itemFactory->create();
        $itemsCollection = $itemModel->getCollection()->addFieldToFilter('slider_id', $sliderId);
        // remove item
        foreach ($itemsCollection as $itemCollection){
            $item_id = $itemCollection['item_id'];
            $is_delete = true;
            foreach ($items as $item){
                if ($item['id'] == $item_id){
                    $is_delete = false;
                    break;
                }
            }
            if ($is_delete){
                $itemModel->load($item_id);
                $itemModel->delete();
            }
        }

        // update or save new item
        foreach ($items as $item){
            $itemModel = $this->itemFactory->create();
            $itemData = [];
            if ($item['id'] && $item['id'] != 0){
                $itemModel->load($item['id']);
                if (!$itemModel->getId()) {
                    $this->messageManager->addError(__('This item no longer exists.'));
                    /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/');
                }
                $itemData['item_id'] = $item['id'];
            }
            $itemData['slider_id'] = $sliderId;
            $itemData['order_number'] = $item['orderId'];
            $itemData['sort_order'] = $item['sortOrder'];
            $data_not_save = [
                'contentBgColorRgb',
                'maxWidthContent',
                'marginLeftContent',
                'marginRightContent',
                'floatContent',
                'imageUploadScope',
                'imageUploaderMageInit',
                'chooseCategoryScope',
                'chooseCategoryInit',
            ];
            $dataSource = [];
            foreach ($item as $key => $value){
                if (!in_array($key, $data_not_save)){
                    $dataSource[$key] = $item[$key];
                }
            }

            $data_block_not_save = [
                'maxWidthBlock',
                'marginLeftBlock',
                'marginRightBlock',
                'floatContentBlock',
            ];

            foreach ($item['blocks'] as $blockKey => $block){
                foreach ($block as $key => $value){
                    if (in_array($key, $data_block_not_save)){
                        unset($dataSource['blocks'][$blockKey][$key]);
                    }
                }
            }

            $itemData['data_source'] = json_encode($dataSource);
            $itemModel->setData($itemData);
            $itemModel->save();
        }
    }
}