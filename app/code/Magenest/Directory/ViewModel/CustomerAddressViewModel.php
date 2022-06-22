<?php

namespace Magenest\Directory\ViewModel;

use Magenest\Directory\Helper\DirectoryHelper;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Directory\Helper\Data as DataHelper;
use Magento\Store\Model\ScopeInterface;

class CustomerAddressViewModel extends \Magento\Customer\ViewModel\Address
{
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;
    /**
     * @var ScopeInterface
     */
    private $scope;

    public function __construct(
        DirectoryHelper $directoryHelper,
        \Magento\Store\Model\StoreManagerInterface $scope,
        DataHelper $helperData,
        AddressHelper $helperAddress
    )
    {
        parent::__construct($helperData,$helperAddress);
        $this->directoryHelper = $directoryHelper;
        $this->scope = $scope;
    }

    public function isDisableDirectoryOnStoreFront()
    {
        $storeId = $this->scope->getStore()->getId();

        return $this->directoryHelper->isDisableDirectoryOnStoreFront($storeId);
    }

}
