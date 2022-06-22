<?php

namespace Magenest\CouponCode\Observer\Coupon;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\PageCache\Model\Cache\Type;
use Magenest\CouponCode\Block\Coupon;
use Magenest\CouponCode\Block\MyCoupon;
use Magenest\CouponCode\Model\ClaimCouponFactory as CouponModel;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon\CollectionFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magenest\CouponCode\ViewModel\Coupon as ViewModel;

class UpdateCustomerUsed implements ObserverInterface
{
    /**
     * @var ViewModel
     */
    protected $viewModel;
    /**
     * @var Redirect
     */
    protected $redirect;
    /**
     * @var CouponModel
     */
    protected $model;
    /**
     * @var ClaimCoupon
     */
    protected $resource;
    /**
     * @var CollectionFactory
     */
    protected $collection;
    /**
     * @var Type
     */
    protected $flush;

    /**
     * @param Type $flush
     * @param ClaimCoupon $resource
     * @param CouponModel $model
     * @param Redirect $redirect
     * @param CollectionFactory $collectionFactory
     * @param ViewModel $viewModel
     */
    public function __construct(
        Type $flush,
        ClaimCoupon $resource,
        CouponModel $model,
        Redirect $redirect,
        CollectionFactory $collectionFactory,
        ViewModel $viewModel
    ) {
        $this->flush = $flush;
        $this->model = $model;
        $this->resource = $resource;
        $this->redirect = $redirect;
        $this->collection = $collectionFactory;
        $this->viewModel = $viewModel;
    }

    /**
     * Count coupon usage per customer
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $data = $observer->getEvent()->getData();
        $rule_id = $data['order']->getData('applied_rule_ids');
        $user = $this->viewModel->getUserId();
        if (isset($rule_id) && isset($user)) {
            $collection = $this->collection->create()->addFieldToFilter('customer_id', $user);
            foreach ($collection as $value) {
                if ($value->getData('rule_id') == $rule_id) {
                    $claimCouponId = $value->getData('id');
                    if (!empty($claimCouponId)) {
                        $model = $this->model->create()->load($claimCouponId);
                        $coupon = $model->getData();
                        $coupon['times_used_per_customer'] += 1;
                        $model->setData($coupon);
                        try {
                            $this->resource->save($model);
                        } catch (\Exception $e) {
                            $this->redirect->setUrl('checkout/index/index');
                        }
                    }
                }
            }
        }
        $this->flush->clean(\Zend_Cache::CLEANING_MODE_ALL, [Coupon::CACHE_TAG,MyCoupon::CACHE_TAG]);
    }
}
