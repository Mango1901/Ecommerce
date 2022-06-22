<?php
namespace Magenest\CouponCode\Model\Configurations;

use Magenest\CouponCode\Model\ConfigurationInterface;

/**
 * Class AbstractDate
 * @package Magenest\CouponCode\Model\ConfigurationList
 */
abstract class AbstractFields implements ConfigurationInterface
{
    /**
     * @var \Magenest\CouponCode\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * AbstractFields constructor.
     *
     * @param \Magenest\CouponCode\Helper\Data $dataHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magenest\CouponCode\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_date = $date;
        $this->_session = $session;
        $this->_resource = $resource;
    }

    /**
     * @inheritdoc
     */
    abstract public function apply($rules);

    /**
     * Get current day from CE
     *
     * @return string
     */
    public function getCurrentDayFromCE()
    {
        $currentDate='';
        if ($this->_dataHelper->checkCommunityEdition()) {
            $currentDate = $this->_date->gmtDate('Y-m-d');
        }
        return (string) $currentDate;
    }

    /**
     * Get configuration field by code
     *
     * @param string $code
     * @return bool
     */
    public function getConfigurationFieldByCode(string $code)
    {
        return (boolean) $this->_dataHelper->getConfigurationFieldByCode($code);
    }

    /**
     * Get current website id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentWebsiteId()
    {
        return (int) $this->_dataHelper->getCurrentWebsiteId();
    }

    /**
     * Get customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_session->getCustomer();
    }

    /**
     * Get table name
     *
     * @param string $tableName
     * @return string
     */
    public function getTableName($tableName)
    {
        return (string) $this->_resource->getTableName($tableName);
    }
}
