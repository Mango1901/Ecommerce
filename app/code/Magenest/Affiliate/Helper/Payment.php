<?php


namespace Magenest\Affiliate\Helper;

use Exception;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\Withdraw;
use Magenest\Affiliate\Model\Withdraw\Status;
use Magenest\PaymentEPay\Api\Data\HandleDisbursementInterface;
use Magenest\RewardPoints\Api\GetReferralCodeByCustomerInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Serializer_Exception;

/**
 * Class Payment
 * @package Magenest\Affiliate\Helper
 */
class Payment extends Data
{
    const CONFIG_PAYMENT_METHODS = 'payment_method';
    const SYSTEM_PAYMENT_METHODS = 'withdraw';
    const PAYMENT_METHOD_SELECT_NAME = 'payment_method';

    /**
     * @var null
     */
    private $_methods = null;

    /**
     * @var null
     */
    private $_activeMethods = null;

    /**
     * @var HandleDisbursementInterface
     */
    private $handleDisbursement;

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param AccountFactory $accountFactory
     * @param CampaignFactory $campaignFactory
     * @param TransactionFactory $transactionFactory
     * @param BlockFactory $blockFactory
     * @param CustomerFactory $customerFactory
     * @param CookieManagerInterface $cookieManagerInterface
     * @param CustomerSession $customerSession
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param CustomerViewHelper $customerViewHelper
     * @param LayoutInterface $layout
     * @param Registry $registry
     * @param HandleDisbursementInterface $handleDisbursement
     * @param GetReferralCodeByCustomerInterface $getReferralCodeByCustomer
     */
    public function __construct(
        Context                            $context,
        ObjectManagerInterface             $objectManager,
        AccountFactory                     $accountFactory,
        CampaignFactory                    $campaignFactory,
        TransactionFactory                 $transactionFactory,
        BlockFactory                       $blockFactory,
        CustomerFactory                    $customerFactory,
        CookieManagerInterface             $cookieManagerInterface,
        CustomerSession                    $customerSession,
        CookieMetadataFactory              $cookieMetadataFactory,
        PriceCurrencyInterface             $priceCurrency,
        StoreManagerInterface              $storeManager,
        TransportBuilder                   $transportBuilder,
        CustomerViewHelper                 $customerViewHelper,
        LayoutInterface                    $layout,
        Registry                           $registry,
        HandleDisbursementInterface        $handleDisbursement,
        GetReferralCodeByCustomerInterface $getReferralCodeByCustomer
    ) {
        $this->handleDisbursement = $handleDisbursement;
        parent::__construct(
            $context,
            $objectManager,
            $accountFactory,
            $campaignFactory,
            $transactionFactory,
            $blockFactory,
            $customerFactory,
            $cookieManagerInterface,
            $customerSession,
            $cookieMetadataFactory,
            $priceCurrency,
            $storeManager,
            $transportBuilder,
            $customerViewHelper,
            $layout,
            $registry,
            $getReferralCodeByCustomer
        );
    }

    /**
     * @param $code
     *
     * @return mixed
     * @throws Zend_Serializer_Exception
     */
    public function getMethodModel($code)
    {
        $method = $this->getAllMethods();
        $methodModel = $this->objectManager->create($method[$code]['model']);

        return $methodModel;
    }

    /**
     * @return mixed|null
     * @throws Zend_Serializer_Exception
     */
    public function getAllMethods()
    {
        $methodConfig = null;
        if ($this->_methods === null) {
            $methodConfig = $this->getPaymentMethod();
            //fixbug unserialize $config  = null for m2 v2.1 EE
            if ($methodConfig !== null) {
                $methodConfig = $this->unserialize($methodConfig);
            }

            /**
             * Get default payment method from default config.xml
             */
            $initialMethod = $this->getModuleConfig('payment_method');

            if ($initialMethod) {
                foreach ($initialMethod as $code => $method) {
                    if (isset($methodConfig[$code])) {
                        $initialMethod[$code] = array_merge($method, $methodConfig[$code]);
                    }
                }
            }

            $this->_methods = $initialMethod;
        }

        return $this->_methods;
    }

    /**
     * @return mixed|null
     * @throws Zend_Serializer_Exception
     */
    public function getActiveMethods()
    {
        if ($this->_activeMethods === null) {
            $methods = $this->getAllMethods();
            foreach ($methods as $code => $config) {
                if (!isset($config['active']) || !$config['active']) {
                    unset($methods[$code]);
                }
            }
            $this->_activeMethods = $methods;
        }

        return $this->_activeMethods;
    }

    /**
     * @param $code
     * @param $amount
     *
     * @return float|int
     * @throws Zend_Serializer_Exception
     */
    public function getFee($code, $amount)
    {
        $methodConfig = $this->getAllMethods();

        if (!empty($methodConfig) && isset($methodConfig[$code]) && isset($methodConfig[$code]['fee'])) {
            $feeConfig = $methodConfig[$code]['fee'];
            if (strpos($feeConfig, '%') !== false) {
                $fee = floatval(trim($feeConfig, '%'));

                return ($amount * $fee / 100);
            } else {
                return floatval($feeConfig);
            }
        }

        return 0;
    }

    /**
     * @param Withdraw $withdraw
     *
     * @return void
     * @throws LocalizedException
     */
    public function checkWithdrawAmount($withdraw)
    {
        $minBalance = $this->getWithdrawMinimumBalance();
        if (
            ($minBalance && $withdraw->getAccount()->getBalance() <= $minBalance) ||
            $withdraw->getAmount() > $withdraw->getAccount()->getBalance()
        ) {
            throw new LocalizedException(__('Your balance is not enough for request withdraw.'));
        }

        $min = $this->getWithdrawMinimum();
        if ($min && $withdraw->getAmount() < $min) {
            throw new LocalizedException(__(
                'The withdraw amount have to equal or greater than %1',
                $this->formatPrice($min)
            ));
        }

        $max = $this->getWithdrawMaximum();
        if ($max && $withdraw->getAmount() > $max) {
            throw new LocalizedException(__(
                'The withdraw amount have to equal or less than %1',
                $this->formatPrice($max)
            ));
        }
    }

    /**
     * @param Withdraw $withdraw
     *
     * @return Withdraw
     * @throws LocalizedException
     */
    public function handleDisbursementForThreshold($withdraw)
    {
        try {
            $threshold = $this->getSafeWithdrawThreshold();
            if ($withdraw->getAmount() < $threshold && $withdraw->getStatus() !== Status::COMPLETE) {
                $result = $this->handleDisbursement->execute($withdraw);
                if (isset($result['ResponseCode']) && $result['ResponseCode'] === 200) {
                    $withdraw->setData('status', Status::COMPLETE);
                } else {
                    $withdraw->setData('status', Status::FAILED);
                }
                $withdraw->save();
            }
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return $withdraw;
    }
}
