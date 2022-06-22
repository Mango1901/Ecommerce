<?php

namespace Magenest\CustomTableRate\Controller\Adminhtml\Rates;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magenest\CustomTableRate\Model\ResourceModel\Carrier;
//use Magento\OfflineShipping\Model\Carrier\TablerateFactory;
use Magenest\CustomTableRate\Model\TableRatesFactory as TablerateFactory;

class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Carrier
     */
    protected $carrierResourceModel;

    /**
     * @var TablerateFactory
     */
    protected $kangarooTableRatesFactory;

    public function __construct(
        Context              $context,
        JsonFactory $jsonFactory,
        Carrier $carrierResourceModel,
        TablerateFactory $kangarooTableRatesFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->carrierResourceModel = $carrierResourceModel;
        $this->kangarooTableRatesFactory = $kangarooTableRatesFactory;
    }

    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $entityId) {
                    /** load your model to update the data */
                    $kangarooRatesModel = $this->kangarooTableRatesFactory->create();
                    $this->carrierResourceModel->load($kangarooRatesModel, $postItems[$entityId]['pk'], 'pk');
                    try {
                        $kangarooRatesModel->setData(array_merge($kangarooRatesModel->getData(), $postItems[$entityId]));
                        $this->carrierResourceModel->save($kangarooRatesModel);
                    } catch (Exception $e) {
                        $messages[] = "[Error:]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
