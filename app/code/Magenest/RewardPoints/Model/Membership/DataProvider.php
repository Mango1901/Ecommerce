<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 * @category Magenest
 * @package  Magenest_RewardPoints
 */

namespace Magenest\RewardPoints\Model\Membership;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magenest\RewardPoints\Model\ResourceModel\Membership\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Json
     */
    protected $_jsonHelper;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $membershipFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        StoreManagerInterface $storeManager,
        CollectionFactory $membershipFactory,
        Json $jsonHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $membershipFactory->create();
        $this->storeManager = $storeManager;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = array();
        /** @var $group */
        foreach ($items as $group) {
            $groupData = $group->getData();
            if (!empty($groupData['tier_logo'])) {
                $groupData['tier_logo'] = empty($groupData['tier_logo']) ? '' : $this->_jsonHelper->unserialize($groupData['tier_logo']);
                $groupData['tier_logo'][0]['path'] = $groupData['tier_logo'][0]['url'];
                if (empty($groupData['tier_logo'][0]['file'])) {
                    $imageUrl = $groupData['tier_logo'][0]['url']; // image from gallery
                } else {
                    $imageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'rewardpoint/membership/' . $groupData['tier_logo'][0]['file'];
                }
                $groupData['tier_logo'][0]['url'] = $imageUrl;
            }
            $this->loadedData[$group->getId()]['membership'] = $groupData;
        }


        return $this->loadedData;

    }
}