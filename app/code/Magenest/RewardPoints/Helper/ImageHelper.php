<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 24/11/2020 10:55
 */

namespace Magenest\RewardPoints\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;

class ImageHelper extends AbstractHelper
{
    const REWARD_POINT_IMAGE_PATH = 'rewardpoint/membership';

    /**
     * Media directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Json
     */
    protected $_jsonHelper;

    /**
     * ImageHelper constructor.
     * @param StoreManagerInterface $storeManager
     * @param Json $jsonHelper
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Context $context
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Json $jsonHelper,
        \Magento\Framework\Filesystem $filesystem,
        Context $context
    ) {
        parent::__construct($context);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
        $this->_jsonHelper = $jsonHelper;
    }

    /**
     * @param $file
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRewardPointsViewFileUrl($file)
    {
        if (empty($file)) {
            return '';
        }
        $imageData = $this->_jsonHelper->unserialize($file);

        if (empty($imageData[0]['file'])) {
            return $imageData[0]['url'];
        }

        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .'rewardpoint/membership/'. $imageData[0]['file'];
    }
}