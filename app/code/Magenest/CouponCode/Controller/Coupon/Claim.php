<?php

namespace Magenest\CouponCode\Controller\Coupon;

use Magenest\CouponCode\Model\ClaimCouponFactory;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\ResourceModel\Coupon as CouponResourceModel;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session;

/**
 * Class Claim
 */
class Claim extends Action
{
    /**
     * Core form key validator
     *
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var ClaimCouponFactory
     */
    protected $couponFactory;

    /**
     * @var ClaimCoupon
     */
    protected $resource;

    /**
     * @var CouponResourceModel
     */
    protected $couponResourceModel;

    /**
     * @var CouponFactory
     */
    protected $couponMagentoFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Session
     */
    protected $customerSession;

    public function __construct(
        Context             $context,
        Validator           $formKeyValidator,
        ClaimCoupon         $resource,
        ClaimCouponFactory  $couponFactory,
        LoggerInterface     $logger,
        CouponFactory       $couponMagentoFactory,
        CouponResourceModel $couponResourceModel,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->resource = $resource;
        $this->couponFactory = $couponFactory;
        $this->logger = $logger;
        $this->couponMagentoFactory = $couponMagentoFactory;
        $this->couponResourceModel = $couponResourceModel;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage('Invalid Form key');
        } else {
            $data = $this->getRequest()->getPostValue();
            if ($data && isset($data['coupon_code'])) {
                $couponCode = $data['coupon_code'];
                $couponMagentoModel = $this->couponMagentoFactory->create();
                $this->couponResourceModel->load($couponMagentoModel, $couponCode, 'code');

                if (!$couponMagentoModel->getCouponId()) {
                    $this->messageManager->addErrorMessage(__('Coupon code doesn\'t exists in system'));
                } else {
                    $connection = $this->resource->getConnection();
                    $ruleId = $couponMagentoModel->getRuleId();
                    $couponId = $couponMagentoModel->getCouponId();
                    $customerId = $this->customerSession->getCustomerId();
                    $select = $connection->select()
                        ->from($connection->getTableName('magenest_customer_coupon'))
                        ->where('rule_id = ?', $ruleId)
                        ->where('coupon_id = ?', $couponId)
                        ->where('customer_id = ?', $customerId);

                    $isExistsInMyCoupon =  $connection->fetchOne($select);
                    if ($isExistsInMyCoupon) {
                        $this->messageManager->addErrorMessage(__('Coupon code does exists in my coupon'));
                    } else {
                        $model = $this->couponFactory->create();
                        $model->addData([
                            'rule_id' => $ruleId,
                            'coupon_id' => $couponId,
                            'customer_id' => $customerId
                        ]);
                        $this->resource->save($model);
                        $this->messageManager->addSuccessMessage(__('You claimed coupon code successfully'));
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('Please enter coupon code to claim.'));
            }
        }

        return $resultRedirect->setPath($this->_redirect->getRefererUrl());

    }
}
