<?php
namespace Magenest\LazyLoading\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\LazyLoading\Helper\Data;

class LazyLoad implements ObserverInterface
{
    protected $_helper;

    public function __construct(
        Data $_helper
    ) {
        $this->_helper = $_helper;
    }

    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $response = $observer->getEvent()->getResponse();
        if(!$response) return;
        if($request->isAjax()) return;
        if(!$this->_helper->isEnabled()) return;
        $html = $response->getBody();
        if($this->_helper->isLazyAllImageEnable()){
            $html = $this->_helper->lazyLoad($html);
        }
        if($this->_helper->isLazyIframeEnable()){
            $html = $this->_helper->lazyLoadIframe($html);
        }

        $response->setBody($html);
    }
}