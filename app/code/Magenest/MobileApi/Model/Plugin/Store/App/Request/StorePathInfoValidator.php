<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Plugin\Store\App\Request;

use Magento\Framework\App\Request\Http;
use Magenest\MobileApi\Model\Helper;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class StorePathInfoValidator
 * @package Magenest\MobileApi\Model\Plugin\Store\App\Request
 */
class StorePathInfoValidator
{
    /**
     * @var StoreRepositoryInterface
     */
    protected $_storeRepository;

    /**
     * StoreRepository
     *
     * @param StoreRepositoryInterface $storeRepository
     */
    function __construct(
        StoreRepositoryInterface $storeRepository
    )
    {
        $this->_storeRepository = $storeRepository;
    }

    /**
     * After get valid store code
     *
     * @param \Magento\Store\App\Request\StorePathInfoValidator $subject
     * @param $result
     * @param \Magento\Framework\App\Request\Http $request
     * @param string $pathInfo
     * @return string|null
     */
    public function afterGetValidStoreCode(
        \Magento\Store\App\Request\StorePathInfoValidator $subject,
        $result,
        Http $request,
        $pathInfo = ''
    )
    {
        $storeCode = $request->getHeader(Helper::STORE_CODE_HEADER);
        if ($storeCode && !empty($storeCode)) {
            $this->_storeRepository->getActiveStoreByCode($storeCode);

            return $storeCode;
        }

        return $result;
    }
}