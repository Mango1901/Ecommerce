<?php


namespace Magenest\Affiliate\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;
use Magenest\Affiliate\Model\Withdraw\Status;

/**
 * Class Withdraw
 * @package Magenest\Affiliate\Model\ResourceModel
 */
class Withdraw extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_affiliate_withdraw', 'withdraw_id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        if ($object->dataHasChangedFor('status') && $object->getStatus() == Status::COMPLETE) {
            $object->setResolvedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        return parent::_beforeSave($object);
    }
}
