<?php

namespace Magenest\CustomOrder\Block\Adminhtml\Order\View;

use FishPig\WordPress\Model\Url;
use Magenest\RewardPoints\Model\Rule;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context as TemplateContext;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magenest\CouponCode\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
class Custom extends Template
{
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(
        TemplateContext $context,
        UrlInterface $urlBuilder,
        OrderRepositoryInterface $orderRepository,
        RuleCollectionFactory $ruleCollectionFactory,
        CouponCollectionFactory $couponCollectionFactory,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderRepository = $orderRepository;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->urlBuilder = $urlBuilder;
    }

    public function getCoupon(){
        $name = $this->getCouponCode();
        $coupon = $this->couponCollectionFactory->create()->addFieldToFilter('code',$name);
        return $coupon->getData()[0]['rule_id'];
    }

    public function getCouponCode(){
        $id = $this->_request->getParam('order_id');
        $order = $this->orderRepository->get($id);
        $couponCode = $order->getCouponCode();
        return $couponCode;
    }

    public function getCouponCodeName(){
        $id = $this->_request->getParam('order_id');
        $order = $this->orderRepository->get($id);
        $couponName = $order->getData('coupon_rule_name');
        return $couponName;
    }
    public function prepareDataSource()
    {
        $coupon_id = $this->getCoupon();
        return $this->urlBuilder->getUrl('sales_rule/promo_quote/edit', [
            'id' => $coupon_id
        ]);
    }
}
