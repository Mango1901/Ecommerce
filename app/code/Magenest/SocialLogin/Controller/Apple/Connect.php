<?php


namespace Magenest\SocialLogin\Controller\Apple;

use Magenest\SocialLogin\Controller\AbstractConnect;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class Connect
 * @package Magenest\SocialLogin\Controller\Apple
 */
class Connect extends AbstractConnect implements CsrfAwareActionInterface
{
    /**
     *
     */
    const APPLE_USER = 'Apple User';

    /**
     * @var string
     */
    protected $_type = 'apple';
    /**
     * @var string
     */
    protected $_path = '';
    /**
     * @var string
     */
    protected $clientModel = '\Magenest\SocialLogin\Model\Apple\Client';

    /**
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param $client
     * @param $code
     *
     * @return mixed
     */
    public function getUserInfo($client, $code)
    {
        $userInfo       = $client->api($this->_path, $code);
        $userInfo['id'] = $userInfo['sub'];
        unset($userInfo['sub']);
        if (!isset($userInfo['email']))
            $userInfo['email'] = $userInfo['id'] . "@apple.com";
        if (!empty($user = $this->getRequest()->getParam('user'))) {
            $user                  = json_decode($user, true);
            $userInfo['firstName'] = $user['name']['firstName'];
            $userInfo['lastName']  = $user['name']['lastName'];
        }
        return $userInfo;
    }

    /**
     * @param $userInfo
     *
     * @return array
     */
    public function getDataNeedSave($userInfo)
    {
        $dataParent     = parent::getDataNeedSave($userInfo);
        $cuttingStrings = explode('@', $userInfo['email']);
        $data           = [
            'email'     => $userInfo['email'],
            'firstname' => $userInfo['firstName'] ?? self::APPLE_USER,
            'lastname'  => $userInfo['lastName'] ?? $cuttingStrings['0']
        ];
        return array_replace_recursive($dataParent, $data);
    }
}
