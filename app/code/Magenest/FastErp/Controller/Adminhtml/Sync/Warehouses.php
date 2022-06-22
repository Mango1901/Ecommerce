<?php

namespace Magenest\FastErp\Controller\Adminhtml\Sync;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use \Magenest\FastErp\Model\Flag\WarehousesFactory;

class Warehouses extends Action
{
    public const ADMIN_RESOURCE = 'Magenest_FastErp::erp_sync_warehouses';

    /**
     * @var WarehousesFactory
     */
    protected $warehousesFactory;

    public function __construct(
        WarehousesFactory $warehousesFactory,
        Context $context
    ) {
        $this->warehousesFactory = $warehousesFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $flag = $this->warehousesFactory->create()->loadSelf();
        if (!$flag->getState()) {
            $flag->setState(1)->save();
            $message = 'Warehouses syncing process has been scheduled! This will take a while to process all the Warehouses';
        } else {
            $message = 'Warehouses syncing is processing by the system. Please wait for complete notification.';
        }

        $result->setData(['message' => $message]);

        return $result;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
