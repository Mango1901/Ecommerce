<?php

namespace Magenest\Slider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;

class Edit extends \Magenest\Slider\Controller\Adminhtml\Slider
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magenest\Slider\Model\Slider
     */
    protected $model = null;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magenest\Slider\Model\Slider $model
     */
    public function __construct(
        Action\Context $context,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Magenest\Slider\Model\Slider $model
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->model = $model;
        parent::__construct($context, $widgetFactory, $logger);
    }

    /**
     * Edit sitemap
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->model;

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('slider/slider/');
                return;
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('magenest_slider_slider', $model);

        // 5. Build edit form
        $this->_initAction()->_addBreadcrumb(
            $id ? __('Edit %1', $model->getName()) : __('New Slider'),
            $id ? __('Edit %1', $model->getName()) : __('New Slider')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Edit Slider'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('New Slider')
        );
        $this->_view->renderLayout();
    }
}
