<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 16/11/2021
 * Time: 13:07
 */

namespace Magenest\FastErp\Controller\Adminhtml\Sync;

use Magenest\FastErp\Api\UpdateQtyProductInSourceInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use \Magenest\FastErp\Model\Flag\StocksFactory;

class Stocks extends Action
{
    public const ADMIN_RESOURCE = 'Magenest_FastErp::erp_sync_stocks';

    /**
     * @var StocksFactory
     */
    protected $stocksFlag;

    /**
     * @param StocksFactory $stocksFlag
     * @param Context $context
     */
    public function __construct(
        StocksFactory $stocksFlag,
        Context $context
    ) {
        $this->stocksFlag = $stocksFlag;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $flag = $this->stocksFlag->create()->loadSelf();
        if (!$flag->getState()) {
            $flag->setState(1)->save();
            $message = 'Stocks syncing process has been scheduled! This will take a while to process all the stocks';
        } else {
            $message = 'Stocks syncing is processing by the system. Please wait for complete notification.';
        }

        $result->setData(['message' => $message]);

        return $result;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
