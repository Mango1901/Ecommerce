<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 16/11/2021
 * Time: 13:23
 */

namespace Magenest\FastErp\Cron;

use Magenest\FastErp\Model\Flag\StocksFactory;
use Magento\Framework\Notification\NotifierInterface;
use Magenest\FastErp\Api\UpdateQtyProductInSourceInterface;

class SyncStockManually
{
    /**
     * @var NotifierInterface
     */
    protected $notifier;

    /**
     * @var UpdateQtyProductInSourceInterface
     */
    protected $updateQtyProductInSource;

    /**
     * @var StocksFactory
     */
    protected $stockFlag;

    /**
     * SyncStock constructor.
     *
     * @param NotifierInterface $notifier
     * @param StocksFactory $stockFlag
     * @param UpdateQtyProductInSourceInterface $updateQtyProductInSource
     */
    public function __construct(
        NotifierInterface $notifier,
        StocksFactory $stockFlag,
        UpdateQtyProductInSourceInterface $updateQtyProductInSource
    ) {
        $this->stockFlag = $stockFlag;
        $this->notifier = $notifier;
        $this->updateQtyProductInSource = $updateQtyProductInSource;
    }

    public function execute()
    {
        $flag = $this->stockFlag->create()->loadSelf();

        if ($flag->getState() == 1) {
            $flag->setState(2)->save();
            $this->notifier->addNotice(
                'ERP Sync Stock',
                "Start syncing stocks from ERP"
            );

            $result = $this->updateQtyProductInSource->execute();

            if ($result) {
                $this->notifier->addNotice(
                    'ERP Sync Stock',
                    "Complete syncing stocks from ERP"
                );
            } else {
                $this->notifier->addNotice(
                    'ERP Sync Stock',
                    "Error when syncing stocks from ERP. Please try again"
                );
            }
            $flag->setState(0)->save();
        }
    }
}
