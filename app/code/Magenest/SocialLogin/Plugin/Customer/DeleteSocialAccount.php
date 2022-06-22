<?php


namespace Magenest\SocialLogin\Plugin\Customer;


/**
 * Class DeleteSocialAccount
 * @package Magenest\SocialLogin\Plugin\Customer
 */
class DeleteSocialAccount
{
    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory
     */
    protected $socialAccountCollection;
    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount
     */
    protected $socialAccountResource;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * DeleteSocialAccount constructor.
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection,
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->socialAccountCollection = $socialAccountCollection;
        $this->socialAccountResource   = $socialAccountResource;
        $this->messageManager          = $messageManager;
    }

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param $result
     * @param $customerId
     * @return bool
     */
    public function afterDeleteById(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        $result,
        $customerId
    ): bool {
        if ($result) {
            $this->deleteSocialAccount($customerId);
        }
        return $result;
    }

    /**
     * @param $customerId
     */
    private function deleteSocialAccount($customerId)
    {
        $socialAccountCollection = $this->socialAccountCollection->create()
                                                                 ->addFieldToFilter('customer_id', $customerId);
        try {
            foreach ($socialAccountCollection as $socialAccount) {
                $this->socialAccountResource->delete($socialAccount);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}