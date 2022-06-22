<?php
namespace Magenest\SocialLogin\Model\Facebook;

use Magenest\SocialLogin\Model\AbstractClient;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Client
 * @package Magenest\SocialLogin\Model\Facebook
 */
class Client extends AbstractClient
{
    /**
     *
     */
    const CHART_COLOR = '#4e71a8';
    /**
     *
     */
    const TYPE_SOCIAL_LOGIN = 'facebook';
    /**
     * @var string
     */
    protected $redirect_uri_path  = 'sociallogin/facebook/connect';
    /**
     * @var string
     */
    protected $path_enalbed       = 'magenest/credentials/facebook/enabled';
    /**
     * @var string
     */
    protected $path_client_id     = 'magenest/credentials/facebook/client_id';
    /**
     * @var string
     */
    protected $path_client_secret = 'magenest/credentials/facebook/client_secret';
    /**
     * @var string
     */
    protected $oauth2_service_uri = 'https://graph.facebook.com';
    /**
     * @var string
     */
    protected $oauth2_auth_uri  = 'https://graph.facebook.com/oauth/authorize';
    /**
     * @var string
     */
    protected $oauth2_token_uri = 'https://graph.facebook.com/oauth/access_token';
    /**
     * @var string[]
     */
    protected $scope = ['public_profile', 'email'];

    /**
     * @return string
     */
    public function createAuthUrl()
    {
        $query = [
            'client_id' => $this->getClientId(),
            'redirect_uri' => str_replace("httpss", "https", str_replace("http", "https", $this->getRedirectUri())),
            'state' => $this->getState(),
            'scope' => implode(',', $this->getScope())
        ];
        $url = $this->oauth2_auth_uri . '?' . http_build_query($query);
        return $url;
    }

    /**
     * @param null $code
     * @throws LocalizedException
     */
    protected function fetchAccessToken($code = null)
    {
        $token_array = [
            'code' => $code,
            'redirect_uri' => str_replace("httpss", "https", str_replace("http", "https", $this->getRedirectUri())),
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'grant_type' => 'authorization_code'
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
        $this->setAccessToken($response['access_token']);
        return $this->getAccessToken();
    }
}
