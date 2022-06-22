<?php

namespace Magenest\AffiliateOpt\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\Affiliate\Model\Campaign\Status;
use Magenest\AffiliateOpt\Api\CampaignRepositoryInterface;
use Magenest\AffiliateOpt\Api\Data\CampaignSearchResultInterface;
use Magenest\AffiliateOpt\Api\Data\CampaignSearchResultInterfaceFactory as SearchResultFactory;
use Magenest\AffiliateOpt\Helper\Data;
use Magenest\AffiliateOpt\Model\CampaignFactory as CampaignAPIFactory;

/**
 * Class CampaignRepository
 * @package Magenest\AffiliateOpt\Model
 */
class CampaignRepository implements CampaignRepositoryInterface
{
    /**
     * @var CampaignFactory
     */
    protected $campaignAPIFactory;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * CampaignRepository constructor.
     *
     * @param Data $helperData
     * @param CampaignFactory $campaignAPIFactory
     * @param SearchResultFactory $searchResultFactory
     * @param Status $status
     */
    public function __construct(
        Data $helperData,
        CampaignAPIFactory $campaignAPIFactory,
        SearchResultFactory $searchResultFactory,
        Status $status
    ) {
        $this->campaignAPIFactory = $campaignAPIFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->status = $status;
        $this->helperData = $helperData;
    }

    /**
     * Find entities by criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return CampaignSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultFactory->create();

        return $this->helperData->processGetList($searchCriteria, $searchResult);
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('Campaign id required'));
        }

        $campaign = $this->campaignAPIFactory->create()->load($id);
        if (!$campaign->getId()) {
            throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
        }

        return $campaign;
    }

    /**
     * {@inheritDoc}
     */
    public function changeStatus($id, $value)
    {
        if (!$value) {
            throw new InputException(__('Value id required'));
        }

        $statusAvailable = $this->status->toOptionHash();
        if (!isset($statusAvailable[$value])) {
            throw new NoSuchEntityException(__('Requested status doesn\'t exist'));
        }

        try {
            $this->get($id)->setStatus($value)->save();
        } catch (Exception $e) {
            throw new CouldNotSaveException((__('Could not change status the campaign: %1', $e->getMessage())));
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($id)
    {
        try {
            $this->get($id)->delete();
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the campaign: %1', $e->getMessage()));
        }

        return true;
    }
}
