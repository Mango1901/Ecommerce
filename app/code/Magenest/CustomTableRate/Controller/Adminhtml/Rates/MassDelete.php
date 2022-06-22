<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Controller\Adminhtml\Rates;

use Magento\Review\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier as RateResource;

/**
 * Class MassDelete
 * @package Magenest\CustomTableRate\Controller\Adminhtml\Rates
 */
class MassDelete extends Action
{
    /**
     * @var RateResource
     */
    protected $_rateResource;

    /**
     * Constructor.
     *
     * @param Action\Context $context
     * @param RateResource $rateResource
     */
    public function __construct(
        Action\Context $context,
        RateResource $rateResource
    )
    {
        $this->_rateResource = $rateResource;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $rateIds = $this->getRequest()->getParam('rates');
        if (!is_array($rateIds)) {
            $this->messageManager->addError(__('Please select rate(s).'));
        } else {
            try {
                $this->_rateResource->getConnection()->delete($this->_rateResource->getMainTable(), ['pk in (?)' => $rateIds]);
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', count($rateIds)));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');

        return $resultRedirect;
    }
}
