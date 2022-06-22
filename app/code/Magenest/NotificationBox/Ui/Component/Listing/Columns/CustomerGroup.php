<?php

namespace Magenest\NotificationBox\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Customer\Model\ResourceModel\Group as ResourceModelGroup;
use Magento\Customer\Model\GroupFactory;
use Magenest\NotificationBox\Helper\Helper;
/**
 * Class CustomerGroup
 * @package Magenest\NotificationBox\Ui\Component\Listing\Columns
 */
class CustomerGroup extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** @var \Magento\Catalog\Helper\Image  */
    protected $imageHelper;

    /** @var UrlInterface  */
    protected $urlBuilder;

    /** @var Json  */
    protected $serialize;

    /** @var ResourceModelGroup */
    protected $resourceModelGroup;

    /** @var GroupFactory */
    protected $group;

    /** @var Helper */
    protected $helper;

    /**
     * @param Helper $helper
     * @param ResourceModelGroup $resourceModelGroup
     * @param GroupFactory $group
     * @param Json $serialize
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Helper $helper,
        ResourceModelGroup $resourceModelGroup,
        GroupFactory $group,
        Json $serialize,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->serialize= $serialize;
        $this->resourceModelGroup = $resourceModelGroup;
        $this->group = $group;
        $this->helper = $helper;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if(isset($item['is_active'])){
                    if($item['is_active'] == \Magenest\NotificationBox\Model\Notification::ACTIVE)
                    {
                        $item['is_active'] = html_entity_decode('<p style="color: green;text-align: center; border:2px solid #2A6002">ACTIVE</p>');
                    }
                    else
                    {
                        $item['is_active'] = html_entity_decode('<p style="color: red;text-align: center; border: 2px solid red">INACTIVE</p>');
                    }
                }
                if(isset($item['customer_group'])){
                    $customerColumns = '';
                    $listCustomer = $this->serialize->unserialize($item['customer_group']);
                    if(count($listCustomer) == count($this->helper->getCustomerGroups())){
                        $item['customer_group'] = 'All Customer Group';
                    }
                    else{
                        foreach ($listCustomer as $customerId)
                        {
                            $customer = $this->group->create();
                            $this->resourceModelGroup->load($customer,$customerId,'customer_group_id');
                            $customerColumns  = $customerColumns.$customer->getCustomerGroupCode().'<br>';
                        }
                        $item['customer_group'] = html_entity_decode($customerColumns);
                    }
                }
            }
        }
        return $dataSource;
    }
}
