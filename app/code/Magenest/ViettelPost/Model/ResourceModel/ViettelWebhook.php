<?php
namespace Magenest\ViettelPost\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magenest\ViettelPost\Model\ViettelWebhook as ViettelWebhookModel;

class ViettelWebhook extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ViettelWebhookModel::TABLE_NAME, ViettelWebhookModel::PRIMARY_KEY);
    }
}
