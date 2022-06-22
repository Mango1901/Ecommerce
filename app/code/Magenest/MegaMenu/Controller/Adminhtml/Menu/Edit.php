<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magenest\MegaMenu\Model\MegaMenu;

/**
 * Class Edit
 * @package Magenest\MegaMenu\Controller\Adminhtml\Menu
 */
class Edit extends Action
{
    const ADMIN_RESOURCE = 'Magenest_MegaMenu::manage';

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var MegaMenu
     */
    protected $_model;

    protected $logCollection;

    protected $serializer;

    /**
     * Constructor.
     *
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param MegaMenu $model
     * @param \Magenest\MegaMenu\Model\ResourceModel\MenuLog\CollectionFactory $logCollection
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        MegaMenu $model,
        \Magenest\MegaMenu\Model\ResourceModel\MenuLog\CollectionFactory $logCollection,
        SerializerInterface $serializer
    ) {
        $this->logCollection = $logCollection;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_model = $model;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_MegaMenu::menu')
            ->addBreadcrumb(__('MegaMenu'), __('MegaMenu'))
            ->addBreadcrumb(__('Manage MegaMenu'), __('Manage MegaMenu'));
        return $resultPage;
    }

    /**
     * Edit Department
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_model;
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This menu is not exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $sessionData = $this->_getSession()->getFormData(true);
        if (!empty($sessionData)) {
            $model->setData($sessionData);
        }
        if ($selectedVersion = $this->getRequest()->getParam('selectedVersion', false)) {
            $this->messageManager->addNoticeMessage(__("You changed to another version, you need to click Save button to save new data for your menu."));
            $logVersion = $this->logCollection->create()->addFieldToFilter('menu_id', $id)->addFieldToFilter('version', $selectedVersion)->getFirstItem();
            if ($logVersion->getId()) {
                $menuData = $logVersion->getMenuData();
                $menuData = $this->serializer->unserialize(base64_decode($menuData));
                unset($menuData['menu_id']);
                $model->addData($menuData);
                $model->setMenuBackupData($logVersion->getMenuStructure());
            }
        }
        $model->setIsBackupVersion((bool)$selectedVersion);
        $this->_coreRegistry->register('magenest_mega_menu', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit MegaMenu') : __('New MegaMenu'),
            $id ? __('Edit MegaMenu') : __('New MegaMenu')
        );
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getMenuName() : __('New MegaMenu'));

        return $resultPage;
    }
}
