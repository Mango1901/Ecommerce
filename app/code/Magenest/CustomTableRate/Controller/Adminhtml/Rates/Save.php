<?php

namespace Magenest\CustomTableRate\Controller\Adminhtml\Rates;

use Magenest\Directory\Model\ResourceModel\City;
use Magenest\Directory\Model\ResourceModel\District;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magenest\CustomTableRate\Model\TableRates;
use Magenest\CustomTableRate\Model\TableRatesFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier as CarrierResourceModel;

/**
 * Save CMS page action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magenest_CustomTableRate::manage';

    /**
     * @var TableRatesFactory
     */
    private $tableRatesFactory;

    /**
     * @var CarrierResourceModel
     */
    private $carrierResourceModel;

    /**
     * @var City
     */
    private $cityResourceModel;

    /**
     * @var District
     */
    private $districtResourceModel;

    /**
     * @param Action\Context $context
     * @param CarrierResourceModel $carrierResourceModel
     * @param City $cityResourceModel
     * @param District $districtResourceModel
     * @param TableRatesFactory|null $tableRatesFactory
     */
    public function __construct(
        Action\Context $context,
        CarrierResourceModel $carrierResourceModel,
        City $cityResourceModel,
        District $districtResourceModel,
        TableRatesFactory $tableRatesFactory = null
    ) {
        $this->tableRatesFactory = $tableRatesFactory ?: ObjectManager::getInstance()->get(TableRatesFactory::class);
        $this->carrierResourceModel = $carrierResourceModel;
        $this->cityResourceModel = $cityResourceModel;
        $this->districtResourceModel = $districtResourceModel;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var TableRates $model */
            $model = $this->tableRatesFactory->create();
            $data['city_code'] = $this->cityResourceModel->getCityCodeWithId($data['city_id']);
            $data['district_code'] = $this->districtResourceModel->getDistrictCodeWithId($data['district_id']);
            $model->setData($data);
            try {
                $this->carrierResourceModel->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the rates.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the page.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['page_id' => $this->getRequest()->getParam('page_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
