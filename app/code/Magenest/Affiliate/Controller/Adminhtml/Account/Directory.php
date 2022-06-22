<?php

namespace Magenest\Affiliate\Controller\Adminhtml\Account;

use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Directory\Model\ResourceModel\District\CollectionFactory as District;
use Magenest\Directory\Model\ResourceModel\Ward\CollectionFactory as Ward;
use Magenest\Affiliate\Controller\Adminhtml\Account;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Directory extends Account
{
    const TYPE_GET_DISTRICT = 'get_district';
    const TYPE_GET_WARD = 'get_ward';

    protected $district;
    protected $ward;
    protected $json;
    protected $resultJsonFactory;

    public function __construct(
        Context                                          $context,
        AccountFactory                                   $accountFactory,
        Registry                                         $coreRegistry,
        PageFactory                                      $resultPageFactory,
        District                                         $district,
        Ward                                             $ward,
        \Magento\Framework\Serialize\Serializer\Json     $json,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context, $accountFactory, $coreRegistry, $resultPageFactory);
        $this->district = $district;
        $this->ward = $ward;
        $this->json = $json;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $type = $this->getRequest()->getParam('type');
        $id = $this->getRequest()->getParam('id');
        $data = [];
        if ($type == self::TYPE_GET_DISTRICT) {
            $district = $this->district->create()->addFieldToFilter('city_id', $id);
            $data = $district->toArray()['items'] ?? [];
        } elseif ($type == self::TYPE_GET_WARD) {
            $ward = $this->ward->create()->addFieldToFilter('district_id', $id);
            $data = $ward->toArray()['items'] ?? [];
        }

        return $resultJson->setData($this->json->serialize($data));
    }
}
