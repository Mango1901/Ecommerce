<?php

namespace Magenest\Customer\Model;

use Magenest\Customer\Helper\Login;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class LoginByTelephone
{
    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LoginByTelephone constructor.
     *
     * @param CollectionFactory $customerCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $customerCollectionFactory,
        LoggerInterface   $logger
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->logger                    = $logger;
    }

    /**
     * @param $telephone
     *
     * @return array|false|mixed|null
     */
    public function authenticateByTelephone($telephone)
    {
        try {
            $telephoneModified = Login::modifyMobileNumber($telephone);
            $collection        = $this->customerCollectionFactory->create()
                ->addAttributeToFilter('telephone', $telephoneModified)
                ->setPageSize(1)->setCurPage(1);
            if ($collection->getSize() == 1) {
                return $collection->getFirstItem()->getData('email');
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return false;
    }
}
