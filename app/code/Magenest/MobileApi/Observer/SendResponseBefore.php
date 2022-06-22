<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Webapi\Rest\Response;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class SendResponseBefore
 * @package Magenest\MobileApi\Observer
 */
class SendResponseBefore implements ObserverInterface
{
    /** @const */
    const MOBILE_API_CACHE_TAG = 'MOBILE_API_CACHE_TAG_ID';

    /**
     * @var array
     */
    protected $_paths = [
        '/V1/mobileapi/stores/home',
        '/V1/products',
        '/V1/categories'.
        '/V1/mobileapi/products',
        '/V1/mobileapi/products/search'
    ];

    /**
     * @var PageCacheConfig
     */
    protected $_pageCacheConfig;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * Constructor.
     *
     * @param PageCacheConfig $pageCacheConfig
     * @param DateTime $dateTime
     */
    function __construct(
        PageCacheConfig $pageCacheConfig,
        DateTime $dateTime
    )
    {
        $this->_pageCacheConfig = $pageCacheConfig;
        $this->_dateTime = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        if ($this->_canCacheRequest($observer)) {
            $ttl = $this->_pageCacheConfig->getTtl();
            $response = $observer->getEvent()->getResponse();
            $response->setHeader('X-Magento-Tags', 'MOBILE_API_CACHE_TAG');
            $response->setHeader('pragma', 'cache', true);
            $response->setHeader('cache-control', 'public, max-age=' . $ttl . ', s-maxage=' . $ttl, true);
            $response->setHeader('expires', $this->_getExpirationHeader('+' . $ttl . ' seconds'), true);
        }
    }

    /**
     * Given a time input, returns the formatted header
     *
     * @param string $time
     * @return string
     * @codeCoverageIgnore
     */
    protected function _getExpirationHeader($time)
    {
        return $this->_dateTime->gmDate(Http::EXPIRATION_TIMESTAMP_FORMAT, $this->_dateTime->strToTime($time));
    }

    /**
     * Cache only rest api
     *
     * @param Observer $observer
     * @return bool
     */
    private function _canCacheRequest(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $response = $observer->getEvent()->getResponse();

        if (!($response instanceof Response)) {
            return false;
        }

        if (!$request->isGet()) {
            return false;
        }

        foreach ($this->_paths as $code => $path) {
            if (preg_match('|' . $path . '|i', $request->getRequestUri())) {
                return true;
            }
        }

        return false;
    }
}