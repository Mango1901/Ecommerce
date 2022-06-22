<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Controller\VAT;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

/**
 * Class Post
 * @package Magenest\OrderExtraInformation\Controller\VAT
 */
class Post extends Action
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param CustomerRepository $customerRepository
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        CustomerRepository $customerRepository,
        Session $customerSession
    )
    {
        $this->_customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get(Session::class)->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $customer = $this->_customerRepository->getById($this->_customerSession->getCustomerId());
            $data = null;
            if ('save' == $this->getRequest()->getParam('action')) {
                $data = \Zend_Json::encode($this->getRequest()->getParam('data'));
            }

            $customer->setCustomAttribute('default_vat_invoice', $data);
            $this->_customerRepository->save($customer);
            $this->messageManager->addSuccessMessage(__('Default VAT invoice was saved successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath('customer/account');
    }
}