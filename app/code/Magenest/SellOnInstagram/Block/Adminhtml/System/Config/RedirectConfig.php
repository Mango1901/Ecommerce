<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\System\Config;
class RedirectConfig extends WebConfig
{
    const TOKEN_PATH = 'sell_instagram/connect/getAccessPage/';
    protected function webConfig()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $redirectUri = $baseUrl . self::TOKEN_PATH;
        $html = "<h2>Please copy and paste these url to your app configuration</h2>";
        $html .= "<div class='input-url'>";
        $html .= "<div><label for='notification_url'>Redirect Uri<input size='100' id='redirect_uri' type='text' readonly value='$redirectUri'></label></div>";
        $html .= "</div>";
        return $html;
    }
}
