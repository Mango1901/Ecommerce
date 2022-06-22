<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Api;

use Magenest\MobileApi\Api\Data\Store\ContactInterface;
use Magenest\MobileApi\Api\Data\Store\MediaEntryInterface;

/**
 * Interface StoreManagementInterface
 * @package Magenest\MobileApi\Api
 */
interface StoreManagementInterface
{
    /**
     * Get home data
     *
     * @return \Magenest\MobileApi\Api\Data\Store\HomeInterface
     */
    public function getHome();

    /**
     * Get locations
     *
     * @param string $storeType
     * @param string $city
     * @param string $cityOther
     * @param string $district
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function locations($storeType, $city, $cityOther, $district);

    /**
     * Contact
     *
     * @param \Magenest\MobileApi\Api\Data\Store\ContactInterface $contact
     * @return bool
     */
    public function contact(ContactInterface $contact);

    /**
     * Get home widget bestseller
     *
     * @param int $limit
     * @param int $page
     * @return \Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface|false
     */
    public function getHomeWidgetBestseller($limit, $page = 1);

    /**
     * Get home widget bestseller
     *
     * @param int $limit
     * @param int $page
     * @return \Magenest\MobileApi\Api\Data\Catalog\Widget\ProductSliderInterface|false
     */
    public function getHomeWidgetNew($limit, $page = 1);
}
