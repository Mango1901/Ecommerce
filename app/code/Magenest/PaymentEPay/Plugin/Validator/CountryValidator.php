<?php

namespace Magenest\PaymentEPay\Plugin\Validator;

use Magento\Directory\Model\AllowedCountries;
use Magento\Store\Model\ScopeInterface;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class CountryValidator
{
    /**
     * @var AllowedCountries
     */
    private $allowedCountryModel;

    /**
     * @var ResultInterfaceFactory
     */
    private $resultInterfaceFactory;

    /**
     * @param AllowedCountries $allowedCountryModel
     * @param ResultInterfaceFactory $resultFactory
     */
    public function __construct(
        AllowedCountries $allowedCountryModel,
        ResultInterfaceFactory $resultFactory
    ) {
        $this->allowedCountryModel = $allowedCountryModel;
        $this->resultInterfaceFactory = $resultFactory;
    }

    public function afterValidate(\Magento\Payment\Gateway\Validator\CountryValidator $subject, $result, array $validationSubject)
    {
        $storeId = $validationSubject['storeId'];
        $allowedCountries = $this->allowedCountryModel->getAllowedCountries(ScopeInterface::SCOPE_STORE, $storeId);
        if (!isset($allowedCountries[$validationSubject['country']])) {
            return $this->resultInterfaceFactory->create(
                [
                    'isValid' => false,
                    'failsDescription' => [],
                    'errorCodes' => []
                ]
            );
        }
        return $result;
    }
}
