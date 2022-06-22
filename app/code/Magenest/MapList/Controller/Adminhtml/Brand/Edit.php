<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 11:09
 */

namespace Magenest\MapList\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magenest\MapList\Controller\Adminhtml\Brand;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Edit
 * @package Magenest\MapList\Controller\Adminhtml\Holiday
 */
class Edit extends Brand
{

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param \Magenest\MapList\Model\BrandFactory  $brandFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\BrandFactory $brandFactory,
        LoggerInterface $logger
    ) {
        $this->_holidayLocationFactory = $brandFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $brandFactory, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->brandFactory->create();
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This holiday doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('brand', $model);

        $data = $this->_session->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('maplist_brand_edit', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()
            ->prepend(
                $model->getId() ? __('Edit Brand', $model->getData('name')) : __('New Brand')
            );

        return $resultPage;
    }
}
