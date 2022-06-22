<?php


namespace Magenest\Affiliate\Model\Banner;

use Magento\Framework\Option\ArrayInterface;
use Magenest\Affiliate\Model\CampaignFactory;

/**
 * Class Campaign
 * @package Magenest\Affiliate\Model\Banner
 */
class Campaign implements ArrayInterface
{
    const CAMPAIGN_COLLECTION = 1;

    /**
     * @var CampaignFactory
     */
    protected $campaign;

    /**
     * Campaign constructor.
     *
     * @param CampaignFactory $campaignFactory
     */
    public function __construct(CampaignFactory $campaignFactory)
    {
        $this->campaign = $campaignFactory;
    }

    /**
     * @return mixed
     */
    protected function getCampaignCollection()
    {
        $campaignModel = $this->campaign->create();

        return $campaignModel->getCollection();
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $campaigns = $this->getCampaignCollection();
        $options = [];
        foreach ($campaigns as $campaign) {
            $options[] = ['value' => $campaign->getId(), 'label' => $campaign->getName()];
        }

        return $options;
    }
}
