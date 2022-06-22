<?php
namespace Magenest\Slider\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Slider extends \Magento\Backend\App\Action
{
    protected $_widgetFactory;

    protected $_logger;

    public function __construct(
        Action\Context $context,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_widgetFactory = $widgetFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magenest_Slider::slider'
        )->_addBreadcrumb(
            __('Buttons'),
            __('Buttons')
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
        return $this->_authorization->isAllowed('Magenest_Slider::slider');
    }

    public function createWidget($data, $slider_id)
    {

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
            ];
            $widget_instance[0]['page_layouts'] = [
                'layout_handle' => ''
            ];
        }

        $widgetInstance = $this->_initWidgetInstance($themeId, $data['widget_id']);
        $widgetInstance->setTitle(
            $data['title']
        )->setStoreIds(
            $data['store']
        )->setSortOrder(
            $data['sort_order']
        )->setPageGroups(
            $widget_instance
        )->setWidgetParameters(
            [
                'slider_id' => $slider_id
            ]
        );
        try {
            $widgetInstance->save();
            return $widgetInstance->getId();
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
        $code = "magenest_category_slider";
        $type = "Magenest\Slider\Block\Widget\SliderView";
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
}
