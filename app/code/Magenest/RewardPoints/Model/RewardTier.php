<?php

namespace Magenest\RewardPoints\Model;

/**
 * Class RewardTier
 * @package Magenest\RewardPoints\Model
 */
class RewardTier implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @return mixed
     */
    public function getRewardTiersEnable()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper        = $objectManager->create('Magenest\RewardPoints\Helper\Data');

        return $helper->getRewardTiersEnable();
    }

    /**
     * @return array|mixed
     */
    public function getConfig()
    {
        $output['getRewardTiersEnable'] = $this->getRewardTiersEnable();

        return $output;
    }
}