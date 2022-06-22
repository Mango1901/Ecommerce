<?php
namespace Magenest\SocialLogin\Controller\Facebook;

use Magenest\SocialLogin\Controller\AbstractConnect;

/**
 * Class Connect
 * @package Magenest\SocialLogin\Controller\Facebook
 */
class Connect extends AbstractConnect
{
    /**
     * @var string
     */
    protected $_exeptionMessage = 'Facebook login failed.';

    /**
     * @var string
     */
    protected $_type = 'facebook';

    /**
     * @var string
     */
    protected $_path = '/me?fields=id,name,first_name,last_name,email';

    /**
     * @var string
     */
    protected $clientModel = '\Magenest\SocialLogin\Model\Facebook\Client';

    /**
     * @param $userInfo
     *
     * @return array
     */
    public function getDataNeedSave($userInfo)
    {
        $dataParent = parent::getDataNeedSave($userInfo);

        $data = [
            'email'     => $userInfo['email'],
            'firstname' => $userInfo['first_name'],
            'lastname'  => $userInfo['last_name'],
        ];

        return array_replace_recursive($dataParent, $data);
    }

    /**
     * @param $client
     * @param $code
     *
     * @return mixed
     */
    public function getUserInfo($client, $code)
    {
        $userInfo = $client->api($this->_path, $code);

        return $userInfo;
    }
}
