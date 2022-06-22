<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\System\Config\Form;

use Magenest\SellOnInstagram\Helper\Helper;
use Magenest\SellOnInstagram\Logger\Logger;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Url;

class GetPageAccessToken extends AbstractButton
{
    const GET_TOKEN_PATH = 'sell_instagram/connect/getAccessPage/';
    /** @var string[] */
    private $_permission = [
        'instagram_basic',
        'instagram_manage_insights',
        'pages_read_engagement',
        'pages_show_list',
        'catalog_management',
        'pages_manage_cta',
        'pages_manage_instant_articles',
        'business_management',
        'pages_messaging',
        'pages_messaging_phone_number',
        'pages_messaging_subscriptions',
        'instagram_manage_comments',
        'instagram_content_publish',
        'instagram_manage_messages',
        'page_events',
        'pages_manage_metadata',
        'pages_read_user_content',
        'pages_manage_posts',
        'pages_manage_engagement',
    ];

    /** @var string */
    protected $_template = "system/config/page-access-token.phtml";

    /** @var string */
    protected $_buttonLabel = "Get Page Access Token";
    /**
     * @var \Magenest\SellOnInstagram\Helper\Data
     */
    protected $helperData;

    /**
     * @return string
     */
    public function __construct(
        Helper $helper,
        Logger $logger,
        WriterInterface $writer,
        Url $frontendUrl,
        Context $context,
        \Magenest\SellOnInstagram\Helper\Data $helperData,
        array $data = [])
    {
        $this->helperData = $helperData;
        parent::__construct($helper, $logger, $writer, $frontendUrl, $context, $data);
    }

    public function getLogin()
    {
        try {
            $params = $this->getRequest()->getParams();
            $key = $params['key'] ?? '{state}';
            $clientId = $this->helperData->getClientID();
            $redirectUri = $this->getBaseUrl() . self::GET_TOKEN_PATH;
            $scope = implode(',', $this->_permission);
            $loginUrl = "https://www.facebook.com/dialog/oauth?client_id=" . $clientId . "&redirect_uri=" . $redirectUri . "&scope=" . $scope . "&state=" . $key;
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $loginUrl = "#";
        }
        return $loginUrl;
    }
}
