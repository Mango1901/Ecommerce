<?php

namespace Magenest\Promobar\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magenest\Promobar\Model\MobilePromobar;

abstract class Promobars extends \Magento\Backend\App\Action
{
    /**
     * Init actions
     *
     * @return $this
     */
    protected $_promobarModel;

    protected $_mobilePromobarModel;

    protected $_widgetFactory;

    protected $_logger;

    protected $_coreRegistry;

    protected $_promobarCollection;

    protected $mobilePromobarModel;

    protected $_promobarResource;

    protected $_mobilePromobarResource;

    public function __construct(Action\Context $context,
                                \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
                                \Psr\Log\LoggerInterface $logger,
                                \Magento\Framework\Registry $coreRegistry,
                                \Magenest\Promobar\Model\PromobarFactory $promobar,
                                \Magenest\Promobar\Model\MobilePromobarFactory $mobilePromobar,
                                \Magenest\Promobar\Model\ResourceModel\Promobar\CollectionFactory $promobarCollection,
                                \Magenest\Promobar\Model\ResourceModel\Promobar $promobarResource,
                                \Magenest\Promobar\Model\ResourceModel\MobilePromobar $mobilePromobarResource
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_promobarModel = $promobar;
        $this->_mobilePromobarModel = $mobilePromobar;
        $this->_widgetFactory = $widgetFactory;
        $this->_logger = $logger;
        $this->_promobarCollection = $promobarCollection;
        $this->mobilePromobarModel = $mobilePromobar;
        $this->_promobarResource = $promobarResource;
        $this->_mobilePromobarResource = $mobilePromobarResource;
        parent::__construct($context);
    }

    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magenest_Promobar::promobar_manage'
        )->_addBreadcrumb(
            __('Promobars'),
            __('Promobars')
        );
        return $this;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Promobar::manage_promobar');
    }

    public function createWidget($data, $id)
    {
        $instanceId = null;
        $themeId = $data['theme'];
        if ($data['pages_display'] == 'default') {
            $widget_instance[0] = [
                'page_group' => 'all_pages'
            ];
            $widget_instance[0]['all_pages'] = [
                'page_id' => '0',
                'layout_handle' => $data['pages_display'],
                'for' => 'all',
                'block' => $data['container_display'],
                'template' => 'Magenest_Promobar::barcontent.phtml'
            ];
            $widget_instance[0]['pages'] = [
                'layout_handle' => ''
            ];
            $widget_instance[0]['page_layouts'] = [
                'layout_handle' => ''
            ];
        } else {
            $widget_instance[0] = [
                'page_group' => 'pages'
            ];
            $widget_instance[0]['pages'] = [
                'page_id' => '0',
                'layout_handle' => $data['pages_display'],
                'for' => 'all',
                'block' => $data['container_display'],
                'template' => 'Magenest_Promobar::barcontent.phtml'
            ];
            $widget_instance[0]['page_layouts'] = [
                'layout_handle' => ''
            ];
        }
        $widgetInstance = $this->_initWidgetInstance($themeId, $instanceId);
        $widgetInstance->setTitle(
            $data['title']
        )->setStoreIds(
            $data['store']
        )->setSortOrder(
            $data['sort_order']
        )->setPageGroups(
            $widget_instance
        )->setWidgetParameters(
            $selectBar = [
                'select_promo_bar' => $id
            ]
        );
        try {
            $widgetInstance->save();
            if ($instanceId == null) {
                $instance_id = $widgetInstance->getId();
            } else {
                $instance_id = $instanceId;
            }
            return $instance_id;
        } catch (\Exception $exception) {
            $this->messageManager->addError($exception->getMessage());
            $this->_logger->critical($exception);
            return;
        }
    }

    protected function _initWidgetInstance($themeId, $instanceId)
    {
        /** @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
        $widgetInstance = $this->_widgetFactory->create();
        $code = "magenest_promobar_promobars";
        $type = "Magenest\Promobar\Block\Widget\BarContent";
        if ($instanceId != null) {
            $widgetInstance->load($instanceId)->setCode($code);
            if (!$widgetInstance->getId()) {
                $this->messageManager->addError(__('Please specify a correct widget.'));
                return false;
            }
        } else {
            // Widget id was not provided on the query-string.  Locate the widget instance
            // type (namespace\classname) based upon the widget code (aka, widget id).
            $widgetInstance->setType($type)->setCode($code)->setThemeId($themeId);
        }
        return $widgetInstance;
    }

    function checkTitleBeforeDuplicate($title)
    {
        if (strpos($title, "#") !== false) {
            $existBars = $this->_promobarModel->create()
                ->getCollection()
                ->addFieldToFilter('title', $title);
            if (count($existBars) > 0) {
                $arrTitle = explode("_duplicate_#", $title);
                $number = $arrTitle[1];
                $count = ++$arrTitle[1];
                $search = "_duplicate_#" . $number;
                $replace = "_duplicate_#" . $count;
                $title = str_replace($search, $replace, $title);
                $title = $this->checkTitleBeforeDuplicate($title);
            } else {
                return $title;
            }
        } else {
            $title = $title . "_duplicate_#0";
            $title = $this->checkTitleBeforeDuplicate($title);
        }

        return $title;
    }

    function savePromobar($modelPromobar){
        $this->_promobarResource->save($modelPromobar);
    }

    function savePromobarMobile($modelPromobarMobile){
        $this->_mobilePromobarResource->save($modelPromobarMobile);
    }

    function loadPromobar($id){
        $model = $this->_promobarModel->create();
        $this->_promobarResource->load($model, $id);
        return $model;
    }

    function loadPromobarMobile($id, $field){
        $model = $this->_mobilePromobarModel->create();
        $this->_mobilePromobarResource->load($model, $id, $field);
        return $model;
    }

}
