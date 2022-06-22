<?php

namespace Magenest\NotificationBox\Ui\Component\Report;

use Magenest\NotificationBox\Model\CustomerToken;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Image
 * @package Magenest\NotificationBox\Ui\Component\Listing\Columns
 */
class SubscribeColumn extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** @var CustomerFactory  */
    protected $customerFactory;

    /** @var Customer  */
    protected $customer;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CustomerFactory $customerFactory
     * @param Customer $customer
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        Customer  $customer,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []

    ) {
        $this->storeManager    = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customer        = $customer;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => $item) {
                if(isset($item['store_id'])){
                    $storeData = $this->storeManager->getStore($item['store_id']);
                    $storeName = $storeData->getName();
                }
                else{
                    $storeName = "";
                }
                $dataSource['data']['items'][$key]['store_id'] = $storeName;

                if(isset($item['status'])){
                    if($item['status'] == CustomerToken::STATUS_SUBSCRIBED ){
                        $item['status'] = CustomerToken::STATUS_SUBSCRIBED_LABEL;
                    }
                    else{
                        $item['status'] = CustomerToken::STATUS_UNSUBSCRIBED_LABEL;
                    }
                }
                $dataSource['data']['items'][$key]['status'] = $item['status'];
            }
        }
        return $dataSource;
    }
}
