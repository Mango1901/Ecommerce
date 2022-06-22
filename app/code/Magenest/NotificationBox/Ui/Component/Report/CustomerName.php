<?php

namespace Magenest\NotificationBox\Ui\Component\Report;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Customer\Api\CustomerRepositoryInterface;
/**
 * Class CustomerName
 * @package Magenest\NotificationBox\Ui\Component\Report
 */
class CustomerName extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
               if(isset($item['customer_id'])){
                   $customerData = $this->customerRepositoryInterface->getById($item['customer_id']);
                   $item['customer_name'] = $customerData->getFirstname() . " " . $customerData->getLastname();
               }
               else{
                   $item['customer_name'] = 'GUEST';
               }
            }
        }
        return $dataSource;
    }
}
