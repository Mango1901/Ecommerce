<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\AdvancedReports\Helper;

class Period extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MIN_DATE_CACHE_KEY = 'aw_arep_period_firstdate';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        parent::__construct($context);
        $this->_localeDate = $localeDate;
        $this->_resource = $resource;
        $this->_cache = $cache;
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string $groupType
     * @param boolean $isShowYear = true
     *
     * @return string
     */
    public function getPeriodAsString($from, $to, $groupType, $isShowYear = true)
    {
        $value = '';
        switch($groupType) {
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_DAY:
                $value = $this->_localeDate->formatDateTime(
                    $from, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE,
                    null, null, $isShowYear?null:'MMM d'
                );
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_WEEK:
                $startDateAsString = $this->_localeDate->formatDateTime(
                    $from, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE,
                    null, null, $isShowYear?null:'MMM d'
                );
                $endDateAsString = $this->_localeDate->formatDateTime(
                    $to, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE,
                    null, null, $isShowYear?null:'MMM d'
                );
                $value = $startDateAsString . ' - ' . $endDateAsString;
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_MONTH:
                $value = $from->format($isShowYear?'M Y':'M');
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_QUARTER:
                $month = (integer)$from->format('m');
                $value = 'Q' . ceil($month / 3) . ' ' . $from->format('Y');
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_YEAR:
                $value = $from->format("Y");
                break;
            default:
                $this->_logger->critical(new \Exception('Unknown group type'));
        }
        return $value;
    }

    /**
     * @return string
     */
    public function getFirstAvailableDateAsString()
    {
        $minDate = $this->_cache->load(self::MIN_DATE_CACHE_KEY);
        if ($minDate) {
            return $minDate;
        }
        $dateTableName = $this->_resource->getTableName('aw_arep_days');
        $minDate = $this->_resource->getConnection('read')->fetchOne("SELECT MIN(date) FROM {$dateTableName}");
        $this->_cache->save($minDate, self::MIN_DATE_CACHE_KEY, [], null);
        return $minDate;
    }
}