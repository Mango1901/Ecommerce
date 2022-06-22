<?php

namespace Magenest\RewardPoints\Controller\Customer;

use Magenest\RewardPoints\Api\ConvertKpointToKcoinInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Psr\Log\LoggerInterface;

class Convert extends \Magento\Framework\App\Action\Action
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var ConvertKpointToKcoinInterface
     */
    protected $convertKpointToKcoin;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param Session $customerSession
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        Session $customerSession,
        Validator $formKeyValidator,
        ConvertKpointToKcoinInterface $convertKpointToKcoin
    ) {
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->convertKpointToKcoin = $convertKpointToKcoin;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            if (!$this->customerSession->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
                $this->messageManager->addErrorMessage(__('Invalid request.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            $data = $this->getRequest()->getPostValue();
            if (!isset($data['kpoint'])) {
                $this->messageManager->addErrorMessage(__('Please input value of kpoint to convert'));
            } else {
                $result = $this->convertKpointToKcoin->execute(
                    $this->customerSession->getCustomerId(),
                    $data['kpoint']
                );

                if ($result) {
                    $this->messageManager->addSuccessMessage(__('Convert Kpoint to Kcoin successfully!'));
                } else {
                    $this->messageManager->addErrorMessage(__('Convert Kpoint to Kcoin fail. Please try again!'));
                }
            }

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            $this->messageManager->addErrorMessage(__('Something is wrong. Convert Kpoint fail'));
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
