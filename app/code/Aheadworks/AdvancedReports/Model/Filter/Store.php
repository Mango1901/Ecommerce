<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Filter;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Model\Currency;

/**
 * Class Store
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class Store
{
    /**
     * @var string
     */
    const SESSION_KEY = 'aw_rep_store_key';

    /**#@+
     * Store types
     */
    const DEFAULT_TYPE = 'default';
    const WEBSITE_TYPE = 'website';
    const GROUP_TYPE = 'group';
    const STORE_TYPE = 'store';
    /**#@-*/

    /**
     * @var []
     */
    private $storeItems;

    /**
     * @var string
     */
    private $currentItemKey;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        RequestInterface $request,
        SessionManagerInterface $session,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve store items
     *
     * @return []
     */
    public function getItems()
    {
        if (!$this->storeItems) {
            $this->storeItems = [
                self::DEFAULT_TYPE => [
                    'type'  => self::DEFAULT_TYPE,
                    'title' => __('All Store Views'),
                    'url'   => $this->urlBuilder->getUrl(
                        '*/*/*',
                        [
                            '_query' => ['website_id' => -1, 'group_id' => '', 'store_id' => ''],
                            '_current' => true
                        ]
                    )
                ],
            ];
            foreach ($this->storeManager->getWebsites() as $website) {
                $this->storeItems[self::WEBSITE_TYPE . '_' . $website->getId()] = [
                    'type'  => self::WEBSITE_TYPE,
                    'title' => $website->getName(),
                    'url'   => $this->urlBuilder->getUrl(
                        '*/*/*',
                        [
                            '_query' => ['website_id' => $website->getId(), 'group_id' => '', 'store_id' => ''],
                            '_current' => true
                        ]
                    )
                ];
                foreach ($website->getGroups() as $group) {
                    /** @var \Magento\Store\Model\Group $group */
                    $this->storeItems[self::GROUP_TYPE . '_' . $group->getId()] = [
                        'type'  => self::GROUP_TYPE,
                        'title' => $group->getName(),
                        'url'   => $this->urlBuilder->getUrl(
                            '*/*/*',
                            [
                                '_query' => ['website_id' => '', 'group_id' => $group->getId(), 'store_id' => ''],
                                '_current' => true
                            ]
                        )
                    ];
                    foreach ($group->getStores() as $store) {
                        /** @var \Magento\Store\Model\Store $store */
                        $this->storeItems[self::STORE_TYPE . '_' . $store->getId()] = [
                            'type' => self::STORE_TYPE,
                            'title' => $store->getName(),
                            'url' => $this->urlBuilder->getUrl(
                                '*/*/*',
                                [
                                    '_query' => ['website_id' => '', 'group_id' => '', 'store_id' => $store->getId()],
                                    '_current' => true
                                ]
                            )
                        ];
                    }
                }
            }
        }
        return $this->storeItems;
    }

    /**
     * Retrieve current item key
     *
     * @return string
     */
    public function getCurrentItemKey()
    {
        if (!$this->currentItemKey) {
            if ($websiteId = $this->request->getParam('website_id')) {
                if (intval($websiteId) == -1) {
                    $this->currentItemKey = self::DEFAULT_TYPE;
                } else {
                    $this->currentItemKey = self::WEBSITE_TYPE . '_' . $websiteId;
                }
            } elseif ($groupId = $this->request->getParam('group_id')) {
                $this->currentItemKey = self::GROUP_TYPE . '_' . $groupId;
            } elseif ($storeId = $this->request->getParam('store_id')) {
                $this->currentItemKey = self::STORE_TYPE . '_' . $storeId;
            }
            if ($this->currentItemKey) {
                $this->session->setData(self::SESSION_KEY, $this->currentItemKey);
                return $this->currentItemKey;
            }
            if ($keyFromSession = $this->session->getData(self::SESSION_KEY)) {
                $this->currentItemKey = $keyFromSession;
                return $this->currentItemKey;
            }
            $this->currentItemKey = self::DEFAULT_TYPE;
        }
        return $this->currentItemKey;
    }

    /**
     * Retrieve store Ids
     *
     * @return \int[]|null
     */
    public function getStoreIds()
    {
        $storeIds = null;
        $data = explode('_', $this->getCurrentItemKey());
        switch ($data[0]) {
            case self::WEBSITE_TYPE:
                $storeIds = $this->storeManager->getWebsite($data[1])->getStoreIds();
                break;
            case self::GROUP_TYPE:
                $storeIds = $this->storeManager->getGroup($data[1])->getStoreIds();
                break;
            case self::STORE_TYPE:
                $storeIds = [$this->storeManager->getStore($data[1])->getId()];
                break;
        }
        return $storeIds;
    }

    /**
     * Retrieve currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        if (!$this->currencyCode) {
            if ($storeIds = $this->getStoreIds()) {
                $storeId = array_shift($storeIds);
                $store = $this->storeManager->getStore($storeId);
                $this->currencyCode = $store->getBaseCurrency()->getCode();
            } else {
                $this->currencyCode = $this->scopeConfig->getValue(
                    Currency::XML_PATH_CURRENCY_DEFAULT,
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT
                );
            }
        }
        return $this->currencyCode;
    }
}
