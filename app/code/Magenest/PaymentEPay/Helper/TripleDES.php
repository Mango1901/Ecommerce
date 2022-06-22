<?php
namespace Magenest\PaymentEPay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Customer\Model\Session;

class TripleDES extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Session
     */
    protected $session;

    protected $json;

    /**
     * Data constructor.
     * @param Json $json
     * @param StoreManagerInterface $storeManager
     * @param Session $session
     * @param Context $context
     */
    public function __construct(
        Json $json,
        StoreManagerInterface $storeManager,
        Session $session,
        Context $context
    ) {
        $this->json = $json;
        $this->storeManager = $storeManager;
        $this->session = $session;
        parent::__construct($context);
    }

    public function encrypt3DES($text, $key) {
        $text =$this->pkcs5_pad($text, 8);
        $size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($size, MCRYPT_RAND);
        $bin = pack('H*', bin2hex($text));
        $encrypted = mcrypt_encrypt(MCRYPT_3DES, $key, $bin, MCRYPT_MODE_ECB, $iv);
        $encrypted = bin2hex($encrypted);
        return $encrypted;
    }

    public function pkcs5_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public function decrypt3DES($text, $key) {
        $str = $this->hex2bin($text);
        $size = @mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
        $iv = @mcrypt_create_iv($size, MCRYPT_RAND);
        $decrypted = @mcrypt_decrypt(MCRYPT_3DES, $key, $str, MCRYPT_MODE_ECB, $iv);
        $info = rtrim($this->pkcs5_unpad($decrypted));
        return $info;
    }

    public function hex2bin($str) {
        $bin = "";
        $i = 0;
        do {
            $bin .= chr(hexdec($str[$i] . $str [($i + 1)]));
            $i += 2;
        } while ( $i < strlen ( $str ) );
        return $bin;
    }

    public function pkcs5_unpad($text) {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }
}
