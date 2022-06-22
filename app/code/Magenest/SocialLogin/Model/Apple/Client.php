<?php


namespace Magenest\SocialLogin\Model\Apple;

use Firebase\JWT\JWT;
use Magenest\SocialLogin\Model\AbstractClient;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;


/**
 * Class Client
 * @package Magenest\SocialLogin\Model\Apple
 */
class Client extends AbstractClient
{
    /**
     *
     */
    const CHART_COLOR = '#000';
    /**
     *
     */
    const TYPE_SOCIAL_LOGIN = 'apple';
    /**
     * @var string
     */
    protected $redirect_uri_path = 'sociallogin/apple/connect';
    /**
     * @var string
     */
    protected $oauth2_auth_uri = 'https://appleid.apple.com/auth/authorize';
    /**
     * @var string
     */
    protected $path_client_id = 'magenest/credentials/apple/client_id';
    /**
     * @var string
     */
    protected $path_enalbed = 'magenest/credentials/apple/enabled';
    /**
     * @var string[]
     */
    protected $oauth2_token_uri = 'https://appleid.apple.com/auth/token';

    /**
     * @var string
     */
    protected $file_key = 'magenest/credentials/apple/file_key';
    /**
     * @var string
     */
    protected $key_id = 'magenest/credentials/apple/key_id';
    /**
     * @var string
     */
    protected $team_id = 'magenest/credentials/apple/team_id';

    /**
     * @var DirectoryList
     */
    protected $directory_list;

    /**
     * @return string|void
     */
    protected $Json;

    /**
     * Client constructor.
     * @param DirectoryList $directory_list
     * @param ZendClientFactory $httpClientFactory
     * @param ConfigInterface $config
     * @param UrlInterface $url
     * @param Json $json
     */
    public function __construct
    (
        DirectoryList $directory_list,
        ZendClientFactory $httpClientFactory,
        ConfigInterface $config,
        UrlInterface $url,
        Json $json
    )
    {
        $this->directory_list = $directory_list;
        parent::__construct($httpClientFactory, $config, $url);
        $this->Json = $json;
    }

    /**
     * @return string|void
     */
    public function createAuthUrl()
    {
        $query = [
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUri(),
            'response_type' => 'code',
            'response_mode' => 'query'
        ];
        $url = $this->oauth2_auth_uri . '?' . http_build_query($query);
        return $url;
    }

    /**
     * @return mixed
     */
    protected function getNameFileKey()
    {
        return $this->_getStoreConfig($this->file_key);
    }

    /**
     * @return mixed
     */
    protected function getKeyId()
    {
        return $this->_getStoreConfig($this->key_id);
    }

    /**
     * @return mixed
     */
    protected function getTeamId()
    {
        return $this->_getStoreConfig($this->team_id);
    }

    /**
     * @param null $code
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */

    protected function fetchAccessToken($code = null)
    {
        $folderFile = $this->directory_list->getPath(DirectoryList::UPLOAD);
        $privateKey = file_get_contents($folderFile . '/' . $this->getNameFileKey());
        $payLoad = [
            'iss' => $this->getTeamId(),
            'aud' => 'https://appleid.apple.com',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $this->getClientId(),
            'kid' => $this->getKeyId()
        ];
        $client_secret = JWT::encode($payLoad, $privateKey, 'ES256');
        $token_array = [
            'code' => $code,
            'client_id' => $this->getClientId(),
            'grant_type' => 'authorization_code',
            'client_secret' => $client_secret,
            'redirect_uri' => $this->getRedirectUri(),
        ];
        if (empty($code)) {
            throw new LocalizedException(
                __('Unable to retrieve access code.')
            );
        }
        $response = $this->_httpRequest(
            $this->oauth2_token_uri,
            'POST',
            $token_array
        );
        $this->setAccessToken($response['id_token']);
        return $this->getAccessToken();
    }

    /**
     * @param $endpoint
     * @param $code
     * @param string $method
     * @param array $params
     * @return array|mixed
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function api($endpoint, $code, $method = 'GET', $params = [])
    {
        if (empty($this->token)) {
            $this->fetchAccessToken($code);
        }
        $header = explode('.', $this->token)[0];
        $header = $this->Json->unserialize(base64_decode($header));
        $header = $this->Json->unserialize($this->Json->serialize($header));
        $body = explode('.', $this->token)[1];
        $body = $this->Json->unserialize(base64_decode($body));
        $body = $this->Json->unserialize($this->Json->serialize($body));
        return array_merge($header, $body);
    }
}
