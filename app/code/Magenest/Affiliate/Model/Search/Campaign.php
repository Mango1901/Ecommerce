<?php


namespace Magenest\Affiliate\Model\Search;

use Magento\Backend\Helper\Data;
use Magento\Framework\DataObject;
use Magenest\Affiliate\Model\ResourceModel\Campaign\CollectionFactory;

/**
 * Class Campaign
 * @package Magenest\Affiliate\Model\Search
 */
class Campaign extends DataObject
{
    /**
     * Campaign Collection factory
     *
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Backend data helper
     *
     * @var Data
     */
    protected $_adminhtmlData;

    /**
     * Campaign constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param Data $adminhtmlData
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Data $adminhtmlData
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_adminhtmlData = $adminhtmlData;

        parent::__construct();
    }

    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $result = [];
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);

            return $this;
        }

        $query = $this->getQuery();
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('name', ['like' => '%' . $query . '%'])
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $campaign) {
            $result[] = [
                'id' => 'magenest_affiliate_campaign/1/' . $campaign->getId(),
                'type' => __('Affiliate Campaign'),
                'name' => $campaign->getName(),
                'description' => $campaign->getDescription(),
                'form_panel_title' => __(
                    'Campaign %1',
                    $campaign->getName()
                ),
                'url' => $this->_adminhtmlData->getUrl(
                    'magenest_affiliate/campaign/edit',
                    ['campaign_id' => $campaign->getId()]
                ),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
