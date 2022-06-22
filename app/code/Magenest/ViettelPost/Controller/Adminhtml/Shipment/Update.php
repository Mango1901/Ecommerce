<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;

class Update extends Action
{
    protected $configWriter;

    public function __construct(
        \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface,
        Action\Context $context
    )
    {
        parent::__construct($context);
        $this->configWriter = $writerInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create('json');
        $provinceId = $this->getRequest()->getParam('province_id');
        $districtId = $this->getRequest()->getParam('district_id');
        $wardsId = $this->getRequest()->getParam('wards_id');
        if($provinceId && $districtId && $wardsId){
            $this->configWriter->save("carriers/viettelpost/information/sender_province", $provinceId);
            $this->configWriter->save("carriers/viettelpost/information/sender_district", $districtId);
            $this->configWriter->save("carriers/viettelpost/information/sender_wards", $wardsId);
        }else{
            return $resultJson->setData([
                'error'=>true,
                'success' => false,
                'message' => 'Please validate input form'
            ]);
        }
        return $resultJson->setData([
            'error'=>false,
            'success' => true
        ]);
    }
}
