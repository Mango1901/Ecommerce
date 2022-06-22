<?php


namespace Magenest\Affiliate\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magenest\Affiliate\Helper\Data as Helper;
use Zend_Serializer_Exception;

/**
 * Class Campaign
 * @package Magenest\Affiliate\Model\ResourceModel
 */
class Campaign extends AbstractDb
{
    /**
     * Date time handler
     *
     * @var DateTime
     */
    protected $_dateTime;

    /**
     *
     * @var Helper
     */
    protected $helper;

    /**
     * Campaign constructor.
     *
     * @param DateTime $dateTime
     * @param Helper $helper
     * @param Context $context
     */
    public function __construct(
        DateTime $dateTime,
        Helper $helper,
        Context $context
    ) {
        $this->_dateTime = $dateTime;
        $this->helper    = $helper;
        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_affiliate_campaign', 'campaign_id');
    }

    /**
     * @param $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCampaignNameById($id)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('campaign_id = :campaign_id');
        $binds   = ['campaign_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->getCouponCode() && $this->exists($object)) {
            throw new LocalizedException(
                __('Something went wrong while saving object. Coupon exist')
            );
        }

        if (is_array($object->getWebsiteIds())) {
            $object->setWebsiteIds(implode(',', $object->getWebsiteIds()));
        }

        if (is_array($object->getAffiliateGroupIds())) {
            $object->setAffiliateGroupIds(implode(',', $object->getAffiliateGroupIds()));
        }

        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        if ($object->isObjectNew()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }
        if ($object->getCommission() && is_array($object->getCommission())) {
            $object->setCommission($this->helper->serialize($object->getCommission()));
        } else {
            $object->setCommission($this->helper->serialize([]));
        }

        foreach (['from_date', 'to_date'] as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->_dateTime->formatDate($value));
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     * @throws Zend_Serializer_Exception
     */
    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        //fixbug unserialize $config  = null for m2 v2.1 EE
        if ($object->getCommission() !== null) {
            $object->setCommission($this->helper->unserialize($object->getCommission()));
        } else {
            $object->setCommission(null);
        }

        return $this;
    }

    /**
     * Check if token exists
     *
     * @param AbstractModel $object
     *
     * @return bool
     * @throws LocalizedException
     */
    public function exists($object)
    {
        $connection = $this->getConnection();
        $select     = $connection->select();
        $select->from($this->getMainTable(), 'coupon_code');
        $select->where('coupon_code = :coupon_code');

        $dataBind = ['coupon_code' => $object->getCouponCode()];
        if ($object->getCampaignId()) {
            $select->where('campaign_id != :campaign_id');
            $dataBind['campaign_id'] = $object->getCampaignId();
        }

        return !($connection->fetchOne($select, $dataBind) === false);
    }
}
