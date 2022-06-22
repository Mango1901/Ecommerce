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

use Magento\Framework\Notification\NotifierInterface;
use Magenest\FastErp\Api\UpdateQtyProductInSourceInterface;

class SyncStock
{
    protected $notifier;

    /**
     * @var UpdateQtyProductInSourceInterface
     */
    protected $updateQtyProductInSource;

    /**
     * SyncStock constructor.
     *
     * @param NotifierInterface $notifier
     * @param UpdateQtyProductInSourceInterface $updateQtyProductInSource
     */
    public function __construct(
        NotifierInterface $notifier,
        UpdateQtyProductInSourceInterface $updateQtyProductInSource
    ) {
        $this->notifier = $notifier;
        $this->updateQtyProductInSource = $updateQtyProductInSource;
    }

    public function execute()
    {
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
    }
}
