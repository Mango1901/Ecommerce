<?php


namespace Magenest\Affiliate\Model\ResourceModel;

use Exception;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class Banner
 * @package Magenest\Affiliate\Model\ResourceModel
 */
class Banner extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_affiliate_banner', 'banner_id');
    }

    /**
     * before save callback
     *
     * @param AbstractModel|\Magenest\Affiliate\Model\Banner $object
     *
     * @return $this
     * @throws Exception
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        if ($object->isObjectNew()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        return parent::_beforeSave($object);
    }
}
