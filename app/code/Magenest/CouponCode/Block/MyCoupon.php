<?php

namespace Magenest\CouponCode\Block;

use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon\Collection;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon\CollectionFactory;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Theme\Block\Html\Pager;
use Magenest\CouponCode\Model\ClaimCoupon as ClaimedCouponModel;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class MyCoupon extends Template implements IdentityInterface
{
    public const CACHE_TAG = 'MAGENEST_MY_COUPON_LISTING';
    /**
     * @var FormKey
     */
    protected $formKey;
    /**
     * @var Context
     */
    private $httpContext;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * Data constructor
     *
     * @param Template\Context $context
     * @param Context $httpContext
     * @param CollectionFactory $collectionFactory
     * @param ResourceConnection $resource
     * @param FormKey $formKey
     * @param array $data
     */
    public function __construct(
        Template\Context   $context,
        Context            $httpContext,
        CollectionFactory  $collectionFactory,
        ResourceConnection $resource,
        FormKey            $formKey,
        PriceHelper        $priceHelper,
        RuleFactory        $ruleFactory,
        array              $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->formKey = $formKey;
        $this->priceHelper = $priceHelper;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * Get pager
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get Tag
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Prepare for paging
     *
     * @return MyCoupon
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $page_data = $this->getCoupon();
        if ($this->getCoupon()) {
            $pager = $this->getLayout()->createBlock(
                Pager::class,
                'custom.mycoupon.page'
            )
                ->setAvailableLimit(Coupon::DEFAULT_ITEM)
                ->setShowPerPage(true)
                ->setCollection($page_data);
            $this->setChild('pager', $pager);
            $this->getCoupon()->load();
        }
        return $this;
    }

    /**
     * Get all coupon claimed
     *
     * @return Collection
     */
    public function getCoupon()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 12;
        return $this->collectionFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->addFieldtoFilter('customer_id', $this->httpContext->getValue('customer_id'))
            ->setOrder('claimed_at', 'DESC')
            ->setPageSize($pageSize)
            ->setCurPage($page);
    }

    /**
     * @param ClaimedCouponModel $coupon
     * @return string
     */
    public function getTitleHtmlOfCoupon($coupon)
    {
        if ($coupon->getData('simple_action') == 'by_percent') {
            $amountDiscount = round($coupon->getData('discount_amount')) . '%';
        } else {
            $amountDiscount = $this->priceHelper->currency($coupon->getData('discount_amount'));
        }
        return __("Input Coupon <span class='discount-code'>%1</span> to get discount <span class='discount-value'>%2</span> for %3", $coupon->getData('code'), $amountDiscount, $coupon->getData('name'));
    }

    /**
     * @param $ruleId
     * @return string
     */
    public function getConditionOfRule($ruleId)
    {
        try {
            $model = $this->ruleFactory->create()
                ->load($ruleId);
            return $model->getConditions()->asStringRecursive();
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return '';
    }
}
