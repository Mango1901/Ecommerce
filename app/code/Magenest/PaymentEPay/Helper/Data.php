<?php
namespace Magenest\PaymentEPay\Helper;

use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * Data constructor.
     * @param Json $json
     * @param StoreManagerInterface $storeManager
     * @param Session $session
     * @param EncryptorInterface $encryptor
     * @param ComponentRegistrar $componentRegistrar
     * @param DriverInterface $driver
     * @param Context $context
     */
    public function __construct(
        Json $json,
        StoreManagerInterface $storeManager,
        Session $session,
        EncryptorInterface $encryptor,
        ComponentRegistrar $componentRegistrar,
        DriverInterface $driver,
        Context $context
    ) {
        $this->json = $json;
        $this->storeManager = $storeManager;
        $this->session = $session;
        $this->_encryptor = $encryptor;
        $this->componentRegistrar = $componentRegistrar;
        $this->driver = $driver;
        parent::__construct($context);
    }

    public function isTest()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_TEST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getTestMerId()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::TEST_MER_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getTestEncodeKey()
    {
        $enCodeKey = $this->scopeConfig->getValue(
            PaymentAttributeInterface::TEST_ENCODE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($enCodeKey);
    }

    public function getTestCancelPassword()
    {
        $cancelPassword = $this->scopeConfig->getValue(
            PaymentAttributeInterface::TEST_CANCEL_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($cancelPassword);
    }

    public function getTestDomain()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::TEST_DOMAIN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getTestCheckTransUrl()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::TEST_CHECKTRANS_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getTestCancelTransUrl()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::TEST_CACELTRANS_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getTestKey3DEn()
    {
        $key3DEn = $this->scopeConfig->getValue(
            PaymentAttributeInterface::TEST_KEY3DES_ENCRYPT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($key3DEn);
    }

    public function getTestKey3DDe()
    {
        $key3DDn = $this->scopeConfig->getValue(
            PaymentAttributeInterface::TEST_KEY3DES_DECRYPT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($key3DDn);
    }

    public function getMerId()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::MER_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getEncodeKey()
    {
        $enCodeKey = $this->scopeConfig->getValue(
            PaymentAttributeInterface::ENCODE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($enCodeKey);
    }

    public function getCancelPassword()
    {
        $cancelPassword = $this->scopeConfig->getValue(
            PaymentAttributeInterface::CANCEL_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($cancelPassword);
    }

    public function getDomain()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::DOMAIN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getCheckTransUrl()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::CHECKTRANS_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getCancelTransUrl()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::CACELTRANS_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getKey3DEn()
    {
        $key3DEn = $this->scopeConfig->getValue(
            PaymentAttributeInterface::KEY3DES_ENCRYPT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($key3DEn);
    }

    public function getKey3DDe()
    {
        $key3DDe = $this->scopeConfig->getValue(
            PaymentAttributeInterface::KEY3DES_DECRYPT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($key3DDe);
    }

    public function checkToken($data)
    {
        $resultCd = $data['resultCd'];
        $timeStamp = $data['timeStamp'];
        $merTrxId = $data['merTrxId'];
        $trxId = $data['trxId'];
        $amount = $data['amount'];
        $merId = $this->isTest() ? $this->getTestMerId() : $this->getMerId();
        $encodeKey = $this->isTest() ? $this->getTestEncodeKey() : $this->getEncodeKey();
        if(array_key_exists("payToken", $data)){
            $str = $resultCd . $timeStamp . $merTrxId . $trxId . $merId . $amount . ((isset($data["userFee"]) && $data["userFee"] > 0) ? $data["userFee"] : '') . $data['payToken'] . $encodeKey;
        } else {
            $str = $resultCd . $timeStamp . $merTrxId . $trxId . $merId . $amount . ((isset($data["userFee"]) && $data["userFee"] > 0) ? $data["userFee"] : '') . $encodeKey;
        }
        $token = hash('sha256', $str);
        $tokenResponse = $data['merchantToken'];
        if ($token != $tokenResponse) {
            return false;
        }
        return true;
    }

    /**
     * Get Disbursement Url
     * @return string
     */
    public function getDisbursementUrl()
    {
        return $this->isTest() ?
            $this->scopeConfig->getValue(
                PaymentAttributeInterface::TEST_DISBURSEMENT_URL,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES
            ) :
            $this->scopeConfig->getValue(
                PaymentAttributeInterface::DISBURSEMENT_URL,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES
            );
    }

    /**
     * Get Partner code
     * @return string
     */
    public function getPartnerCode()
    {
        return $this->isTest() ?
            $this->scopeConfig->getValue(
                PaymentAttributeInterface::TEST_PARTNER_CODE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES
            ) :
            $this->scopeConfig->getValue(
                PaymentAttributeInterface::PARTNER_CODE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES
            );
    }

    /**
     * @return string
     */
    public function readKeyContentFromFile($fileName)
    {
        try {
            $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Magenest_PaymentEPay');
            $fileAbsolutePath = $moduleDir . $fileName;
            if (!$this->driver->isReadable($fileAbsolutePath)) {
                $this->driver->changePermissions($fileAbsolutePath, 0777);
            }
            return $this->driver->fileGetContents($fileAbsolutePath);
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getPublicKey()
    {
        return $this->readKeyContentFromFile('/keyRSA/public.pem');
    }

    /**
     * @return string|null
     */
    public function getPrivateKey()
    {
        return $this->readKeyContentFromFile('/keyRSA/private.pem');
    }

    /**
     * @param array $dataSend
     * @return array
     */
    public function handleRequest($dataSend)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getDisbursementUrl());
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataSend);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1800);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
            $result = curl_exec($ch);
            $curlErrno = curl_errno($ch);
            $curlError = curl_error($ch);
            curl_close($ch);
            return [
                'result' => $result,
                'curlErrno' => $curlErrno,
                'curlError' => $curlError
            ];
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return [];
    }

    public function getEpayDomain()
    {
        if ($this->isTest()) {
            $domain = $this->getTestDomain();
        } else {
            $domain = $this->getDomain();
        }
        return $domain;
    }

    public function canOrderEpay($order): bool
    {
        return $order->getState() == Order::STATE_NEW && $order->getStatus() == "pending_payment"
            && $order->getPayment()->getMethod() == "vnpt_epay";
    }

    public function QRPaymentIsTest()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::QR_PAYMENT_IS_TEST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getQRPaymentTestMerId()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::QR_PAYMENT_TEST_MER_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getQRPaymentMerId()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::QR_PAYMENT_MER_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getQRPaymentTestEncodeKey()
    {
        $enCodeKey = $this->scopeConfig->getValue(
            PaymentAttributeInterface::QR_PAYMENT_TEST_ENCODE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($enCodeKey);
    }

    public function getQRPaymentEncodeKey()
    {
        $enCodeKey = $this->scopeConfig->getValue(
            PaymentAttributeInterface::QR_PAYMENT_ENCODE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($enCodeKey);
    }

    public function getQRPaymentTestDomain()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::QR_PAYMENT_TEST_DOMAIN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getQRPaymentDomain()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::QR_PAYMENT_DOMAIN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISPaymentTestDomain()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_TEST_DOMAIN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISPaymentDomain()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_DOMAIN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function ISPaymentIsTest()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_IS_TEST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISPaymentTestDomainListing()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_TEST_LISTING_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISPaymentDomainListing()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_LISTING_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISPaymentTestMerId()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_TEST_MER_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISPaymentMerId()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_MER_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISTestCancelTransUrl()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_TEST_CHECKTRANS_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISCancelTransUrl()
    {
        return $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_CHECKTRANS_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
    }

    public function getISPaymentTestEncodeKey()
    {
        $enCodeKey = $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_TEST_ENCODE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($enCodeKey);
    }

    public function getISPaymentEncodeKey()
    {
        $enCodeKey = $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_PAYMENT_ENCODE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($enCodeKey);
    }

    public function getISTestCancelPassword()
    {
        $cancelPassword = $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_TEST_CANCEL_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($cancelPassword);
    }

    public function getISCancelPassword()
    {
        $cancelPassword = $this->scopeConfig->getValue(
            PaymentAttributeInterface::IS_CANCEL_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES
        );
        return $this->_encryptor->decrypt($cancelPassword);
    }




}
