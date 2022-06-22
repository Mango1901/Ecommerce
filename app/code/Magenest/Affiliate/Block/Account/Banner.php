<?php

namespace Magenest\Affiliate\Block\Account;

use Magento\Framework\Exception\LocalizedException;
use Magenest\Affiliate\Block\Account;
use Magenest\Affiliate\Model\Config\Source\Urltype;
use Magenest\Affiliate\Model\Banner\Status;
use Zend\Validator\Uri;

/**
 * Class Banner
 * @package Magenest\Affiliate\Block\Account
 */
class Banner extends Account
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Affiliate Banners'));

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getAvailableBanners()
    {
        $campaigns = $this->campaignFactory->create()->getCollection()
            ->getAvailableCampaign(
                $this->getCurrentAccount()->getGroupId(),
                $this->_storeManager->getWebsite()->getId()
            )
            ->getColumnValues('campaign_id');

        $banner = $this->objectManager->create('Magenest\Affiliate\Model\Banner');
        $bannerCollection = $banner->getCollection()
            ->addFieldToFilter('campaign_id', ['in' => $campaigns])
            ->addFieldToFilter('status', Status::ENABLED);

        return $bannerCollection;
    }

    /**
     * @param $banner
     *
     * @return string
     */
    public function getLink($banner)
    {
        $url = $banner->getLink();
        $validator = new Uri();
        if (!$validator->isValid($url)) {
            $url = $this->getUrl('/');
        }

        return $this->_affiliateHelper->getSharingUrl(
            $url,
            ['source' => 'banner', 'key' => $banner->getId()],
            Urltype::TYPE_PARAM
        );
    }

    /**
     * @param $banner
     *
     * @return string
     */
    public function getContentText($banner)
    {
        return $this->getBannerLink($banner, $banner->getContentHtml());
    }

    /**
     * @param $banner
     * @param $text
     *
     * @return string
     */
    public function getBannerLink($banner, $text)
    {
        $html = '<a href="' . $this->getLink($banner) . '" ' . ($banner->getRelNofollow() ? "rel='nofollow' " : '') . 'target="_blank">';
        $html .= $text;
        $html .= '</a>';

        return $html;
    }
}
