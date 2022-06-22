<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 11:09
 */

namespace Magenest\MapList\Controller\Adminhtml\Location;

use Magento\Backend\App\Action;
use Magenest\MapList\Controller\Adminhtml\Location;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Edit
 * @package Magenest\MapList\Controller\Adminhtml\Location
 */
class Edit extends Location
{


    /**
     * @var \Magenest\MapList\Model\StoreProductFactory
     */
    protected $storeProductFactory;


    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param \Magenest\MapList\Model\LocationFactory $locationFactory
     * @param \Magenest\MapList\Model\StoreProductFactory  $storeProductFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        \Magenest\MapList\Model\StoreProductFactory $storeProductFactory,
        LoggerInterface $logger
    ) {
        $this->storeProductFactory = $storeProductFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $locationFactory, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->locationFactory->create();
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This location doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }

            try {
                $storeProductData = $this->storeProductFactory->create()->getCollection()
                    ->addFieldToFilter('location_id', $id)->getData();
            } catch (\Exception $e) {
                $storeProductData = array();
            }

            $this->coreRegistry->register('maplist_location_selected_product', $storeProductData);
        }

        $this->coreRegistry->register('location', $model);

        $data = $this->_session->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('maplist_location_edit', $model);
        $location = array(
            'latitude' => $model['latitude'],
            'longitude' => $model['longitude'],
        );
        $this->coreRegistry->register('maplist_location_location', $location);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()
            ->prepend(
                $model->getId() ? __('Edit Store', $model->getData('name')) : __('New Store')
            );

        return $resultPage;
    }
}
