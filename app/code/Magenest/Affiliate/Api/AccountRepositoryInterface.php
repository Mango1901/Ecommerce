<?php


namespace Magenest\Affiliate\Api;

/**
 * Interface AccountRepositoryInterface
 * @api
 */
interface AccountRepositoryInterface
{
    /**
     * @param int|null $storeId
     * @return \Magenest\Affiliate\Api\Data\AccountInterface Account.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($storeId = null);

    /**
     * @param bool $isSubscribe
     * @param int|null $storeId
     * @return bool false for unsubscribed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function subscribe($isSubscribe, $storeId = null);

    /**
     * @param string $contacts
     * @param string|null $referUrl
     * @param string|null $subject
     * @param string|null $content
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invite($contacts, $referUrl = null, $subject = null, $content = null, $storeId = null);

    /**
     * @param string|null $email
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function signup($email = null, $storeId = null);

    /**
     * @param string $url
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function createReferLink($url, $storeId = null);

    /**
     * Lists transaction that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @param int|null $storeId
     *
     * @return \Magenest\Affiliate\Api\Data\TransactionSearchResultInterface Transaction search result
     *     interface.
     */
    public function transactions(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Lists withdraw that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @param int|null $storeId
     *
     * @return \Magenest\Affiliate\Api\Data\WithdrawSearchResultInterface Withdraw search result
     *     interface.
     */
    public function withdrawsHistory(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Required(account_id, amount, payment_method)
     * Paypal method required paypal_email field
     *
     * @param \Magenest\Affiliate\Api\Data\WithdrawInterface $data
     * @param int|null $storeId
     *
     * @return int Withdraw id created
     * @throws \Magento\Framework\Exception\LocalizedException;
     */
    public function withdraw(\Magenest\Affiliate\Api\Data\WithdrawInterface $data, $storeId = null);

    /**
     * Lists campaign that match customer.
     *
     * @param int|null $storeId
     *
     * @return \Magenest\Affiliate\Api\Data\CampaignSearchResultInterface Withdraw search result
     */
    public function campaigns($storeId = null);

    /**
     * Lists campaign that match guest.
     *
     * @param int|null $storeId
     *
     * @return \Magenest\Affiliate\Api\Data\CampaignSearchResultInterface Withdraw search result
     */
    public function guestCampaigns($storeId = null);
}
