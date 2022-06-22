<?php
namespace Magenest\CouponCode\Block;

use Magento\Framework\View\Element\Template;
use Magenest\CouponCode\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon\CollectionFactory as ClaimCouponCollectionFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Coupon extends Template implements IdentityInterface
{
    public const CACHE_TAG = 'MAGENEST_COUPON_LISTING';
    public const DEFAULT_ITEM = [12=>12,24=>24,36=>36];
    /**
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var Context
     */
    private $httpContext;
    /**
     * @var CouponCollectionFactory
     */
    private $couponCollectionFactory;

    /**
     * @var ClaimCouponCollectionFactory
     */
    private $claimCouponFactory;

    /**
     * Data constructor
     *
     * @param Context $httpContext
     * @param DateTime $dateTime
     * @param Template\Context $context
     * @param CouponCollectionFactory $couponCollectionFactory
     * @param ClaimCouponCollectionFactory $claimCouponFactory
     * @param array $data
     */
    public function __construct(
        Context $httpContext,
        DateTime $dateTime,
        Template\Context $context,
        CouponCollectionFactory $couponCollectionFactory,
        ClaimCouponCollectionFactory $claimCouponFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateTime = $dateTime;
        $this->httpContext = $httpContext;
        $this->claimCouponFactory = $claimCouponFactory;
        $this->couponCollectionFactory = $couponCollectionFactory;
    }

    /**
     * Prepare for paging
     *
     * @return Coupon
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $page_data = $this->getCoupon();
        if ($this->getCoupon()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'custom.coupon.page'
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
     * Get pager
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get all coupon is active
     *
     * @return \Magenest\CouponCode\Model\ResourceModel\Coupon\Collection $coupon
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCoupon()
    {
        $today = $this->dateTime->date('Y-m-d h:m:s', strtotime('-1 day'));
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 12;
        return $this->couponCollectionFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('coupon_display', 0)
            ->addFieldToFilter('to_date', [
                  'or' => [
                      0 => ['gteq' => $today],
                      1 => ['is' => new \Zend_Db_Expr('null')]
                  ]
            ])
            ->setPageSize($pageSize)
            ->setCurPage($page);
    }

    /**
     * Check coupon is claimed
     *
     * @param int $couponClaimedId
     * @return bool
     */
    public function checkClaimedCoupon(int $couponClaimedId)
    {
        $userId = $this->httpContext->getValue('customer_id');
        $claimCoupon = $this->claimCouponFactory->create()
            ->addFieldToFilter('customer_id', $userId)
            ->addFieldToFilter('main_table.coupon_id', $couponClaimedId)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
        return count($claimCoupon->getData()) != 0 ? true : false;
    }

    /**
     * Get Tag
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
        // TODO: Implement getIdentities() method.
    }
}
