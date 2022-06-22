<?php

namespace Magenest\SellOnInstagram\Model\ResourceModel\History;

use Magenest\SellOnInstagram\Model\ResourceModel\History;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Link table name
     *
     * @var string
     */
    protected $linkTable;

    protected function _construct()
    {
        $this->_init(
            \Magenest\SellOnInstagram\Model\History::class, History::class
        );
        $this->linkTable = $this->getTable('admin_user');
    }

    /**
     * @return $this|Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['link_table' => $this->linkTable], 'link_table.user_id = main_table.user_id', ['username']
        );

        return $this;
    }
}
