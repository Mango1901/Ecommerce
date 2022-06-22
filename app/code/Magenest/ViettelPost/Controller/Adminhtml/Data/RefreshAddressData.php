<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Controller\Adminhtml\Data;

use Magenest\ViettelPost\Model\District;
use Magenest\ViettelPost\Model\Province;
use Magenest\ViettelPost\Model\Wards;
use Magento\Backend\App\Action;

class RefreshAddressData extends Action
{
    protected $_helperData;

    public function __construct(
        \Magenest\ViettelPost\Helper\Data $helperData,
        Action\Context $context
    )
    {
        parent::__construct($context);
        $this->_helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $addressData = $this->_helperData->getAddressData($this->_helperData->getProvinceUrlApi());
        if($addressData){
            $this->_helperData->saveAddressInfoData(Province::TABLE_NAME, $addressData);
        }else{
            $this->messageManager->addErrorMessage("Error: Cannot update Province Data");
        }

        $addressData = $this->_helperData->getAddressData($this->_helperData->getDistrictUrlApi());
        if($addressData) {
            $this->_helperData->saveAddressInfoData(District::TABLE_NAME, $addressData);
        }else{
            $this->messageManager->addErrorMessage("Error: Cannot update District Data");
        }

        $addressData = $this->_helperData->getAddressData($this->_helperData->getWardsUrlApi());
        if($addressData) {
            $this->_helperData->saveAddressInfoData(Wards::TABLE_NAME, $addressData);
        }else{
            $this->messageManager->addErrorMessage("Error: Cannot update Wards Data");
        }

        return $this->resultRedirectFactory->create()->setPath('');
    }
}
