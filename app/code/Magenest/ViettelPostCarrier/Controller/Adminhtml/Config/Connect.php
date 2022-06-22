<?php
namespace Magenest\ViettelPostCarrier\Controller\Adminhtml\Config;

use Magenest\ViettelPost\Helper\Data;
use Magenest\ViettelPost\Model\District;
use Magenest\ViettelPost\Model\Province;
use Magenest\ViettelPost\Model\Wards;
use Magenest\ViettelPostCarrier\Model\ViettelPostApi;
use Magento\Backend\App\Action;

class Connect extends Action
{
    /** @var Data */
    private $_helperData;

    /** @var ViettelPostApi */
    private $viettelPostApi;

    public function __construct(
        Data $helperData,
        ViettelPostApi $viettelPostApi,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->_helperData = $helperData;
        $this->viettelPostApi = $viettelPostApi;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $this->viettelPostApi->connect();
            $this->messageManager->addSuccessMessage(__("ViettelPost API connect successfully!"));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $addressData = $this->viettelPostApi->getPublicProvinceCode();
        if ($addressData) {
            $this->_helperData->saveAddressInfoData(Province::TABLE_NAME, $addressData);
        } else {
            $this->messageManager->addErrorMessage("Error: Cannot update Province Data");
        }

        $addressData = $this->viettelPostApi->getPublicDistrictCode();
        if ($addressData) {
            $this->_helperData->saveAddressInfoData(District::TABLE_NAME, $addressData);
        } else {
            $this->messageManager->addErrorMessage("Error: Cannot update District Data");
        }

        $addressData = $this->viettelPostApi->getPublicWardCode();
        if ($addressData) {
            $this->_helperData->saveAddressInfoData(Wards::TABLE_NAME, $addressData);
        } else {
            $this->messageManager->addErrorMessage("Error: Cannot update Wards Data");
        }

        return $this->resultRedirectFactory->create()->setRefererOrBaseUrl();
    }
}
