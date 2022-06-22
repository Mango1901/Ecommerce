<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ReservationStockUi extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ReservationStockUi
 */

namespace Magenest\ReservationStockUi\Controller\Adminhtml\Reservation;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magenest\ReservationStockUi\Controller\Adminhtml\Inventory;

class Index extends Inventory implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Magenest_ReservationStockUi::catalog_reservation';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Reservation Stock'));

        return $resultPage;
    }
}
