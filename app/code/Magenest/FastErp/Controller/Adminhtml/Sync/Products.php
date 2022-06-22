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


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use \Magenest\FastErp\Model\Flag\ProductsFactory;

class Products extends Action
{
    public const ADMIN_RESOURCE = 'Magenest_FastErp::erp_sync_products';

    protected $productsFlag;

    public function __construct(
        ProductsFactory $productsFactory,
        Context $context
    ) {
        $this->productsFlag = $productsFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $flag = $this->productsFlag->create()->loadSelf();
        if (!$flag->getState()) {
            $flag->setState(1)->save();
            $message = 'Products syncing process has been scheduled! This will take a while to process all the products';
        } else {
            $message = 'Products syncing is processing by the system. Please wait for complete notification.';
        }

        $result->setData(['message' => $message]);

        return $result;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
