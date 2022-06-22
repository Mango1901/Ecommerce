<?php

namespace Magenest\PaymentEPay\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class CurrencyValidator
 *
 * @package Magento\Payment\Gateway\Validator
 */
class CurrencyValidator extends AbstractValidator
{
    /**
     * @var ConfigInterface
     */
    private $config;

    protected $_storeManager;

    /**
     * CurrencyValidator constructor.
     *
     * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
     * @param ConfigInterface $config
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        ConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->_storeManager = $storeManager;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $isValid = true;
        $baseCurrency = $this->_storeManager->getStore()->getBaseCurrencyCode();
        if($baseCurrency == "VND"){
            $isValid = true;
        }else{
            $isValid = false;
        }
        return $this->createResult($isValid);
    }
}
