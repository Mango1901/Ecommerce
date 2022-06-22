<?php

namespace Magenest\Affiliate\Controller\Adminhtml\Transaction;

use Magenest\PaymentEPay\Model\HandleBalanceFetch;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class BalanceUpdate extends Action
{
    protected $cache;

    protected $price;

    protected $handleBalanceFetch;

    public function __construct(
        Context $context,
        PriceHelper $price,
        CacheInterface $cache,
        HandleBalanceFetch $handleBalanceFetch
    ) {
        $this->price = $price;
        $this->cache = $cache;
        $this->handleBalanceFetch = $handleBalanceFetch;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $balanceData = $this->handleBalanceFetch->execute();
        $cacheKey = "affiliate_balance";
        if (isset($balanceData['ResponseCode']) && $balanceData['ResponseCode'] == 200) {
            $balance = $this->price->currency($balanceData['CurrentBalance']);

            $result->setData([
                'success' => true,
                'balance' => $balance
            ]);
            $this->cache->save($balance, $cacheKey, [$cacheKey, Config::CACHE_TAG]);
        } else {
            $result->setData([
                'success' => false,
                'message' => $balanceData['ResponseMessage']
            ]);
        }
        return $result;
    }
}
