<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;

/**
 * Class UpdateLayout
 * @package Magenest\MobileApi\Observer
 */
class UpdateLayout implements ObserverInterface
{
    /**
     * @var  RequestInterface
     */
    protected $_request;

    /**
     * Constructor.
     *
     * @param RequestInterface $request
     */
    function __construct(
        RequestInterface $request
    )
    {
        $this->_request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $isMobileView = filter_var($this->_request->getParam('is_mobile_view'), FILTER_VALIDATE_BOOLEAN);
        if($isMobileView){
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addHandle('mobileapi_view_update');
        }
    }
}