<?php

namespace Magenest\SellOnInstagram\Controller\Connect;

use Magenest\SellOnInstagram\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;

class GetAccessPage extends AbstractClient
{
    const NO_ERROR_MESS = 0;
    const ERROR_MESS = 1;

    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            if (isset($params['code'])) {
                $pageId = $this->helperData->getPageId();
                $appSecret = $this->helperData->getClientSecret();
                $appId = $this->helperData->getClientID();
                $redirectUri = $this->urlBuilder->getBaseUrl() . 'sell_instagram/connect/getAccessPage/';
                if (isset($pageId) && isset($appSecret) && isset($appId)) {
                    $urlGetToken = "https://graph.facebook.com/v11.0/oauth/access_token?client_id=" . $appId . "&redirect_uri=" . $redirectUri . "&client_secret=" . $appSecret . "&code=" . $params['code'];
                    $this->sendGet($urlGetToken);
                    $userParams = $this->helper->unserialize($this->curl->getBody());
                    $urlAccess = "https://graph.facebook.com/v11.0/" . $pageId . "?fields=access_token&access_token=" . $userParams['access_token'];
                    $this->sendGet($urlAccess);
                    $pageParams = $this->helper->unserialize($this->curl->getBody());
                    if (!isset($pageParams['error'])) {
                        $this->writer->save(Data::XML_PATH_TOKEN, $pageParams['access_token'], ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
                        $this->cleanConfigCache();
                        $errorMess = self::NO_ERROR_MESS;
                    } else {
                        $errorMess = self::ERROR_MESS;
                        $mess = "Please check your Page Id, App Id and App secret";
                    }
                } else {
                    $errorMess = self::ERROR_MESS;
                    $mess = "Please enter your Page Id, App Id and App secret";
                }
            } else {
                $errorMess = self::ERROR_MESS;
                $mess = "Connect Instagram: Message params does not exist.";
            }
        } catch (\Exception $exception) {
            $errorMess = self::ERROR_MESS;
            $mess = $exception->getMessage();
        }
        $arguments = [
            'key' => $params['state'],
            'errorMess' => $errorMess,
            'message' => $mess ?? null
        ];
        $urlRedirect = $this->urlBuilder->getUrl(self::CONFIGURATION_SECTION, $arguments);
        $this->_redirect($urlRedirect);
    }

    protected function sendGet($url)
    {
        $this->curl->get($url);
        $this->logger->critical('Result ' . print_r([$this->curl->getBody()], true));
    }
}
