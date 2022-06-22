<?php
namespace Magenest\SocialLogin\Model\Share;

use Magento\Backend\App\ConfigInterface;
use Magento\Framework\DataObject;

class Share extends DataObject
{
    const XML_PATH_ENABLED     = 'magenest/general/enabled_share';
    const XML_PATH_SOCIALSHARE = 'magenest/general/select_social_share';
    const XML_PATH_ZALO_OA     = 'magenest/credentials/zalo/oaid';

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;

    /**
     * Share constructor.
     *
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        ConfigInterface $config,
        array           $data = []
    ) {
        $this->_config = $config;
        parent::__construct($data);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_isEnabled();
    }

    /**
     * @return mixed
     */
    protected function _isEnabled()
    {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * @param $xmlPath
     *
     * @return mixed
     */
    protected function _getStoreConfig($xmlPath)
    {
        return $this->_config->getValue($xmlPath);
    }

    /**
     * @return array|int[]|string[]
     */
    public function getSocialShare()
    {
        $socialShare = $this->_getStoreConfig(self::XML_PATH_SOCIALSHARE);
        if ($socialShare == null) {
            return [];
        }
        return array_flip(explode(',', $socialShare));
    }

    /**
     * @return mixed
     */
    public function getZaloOaId()
    {
        return $this->_getStoreConfig(self::XML_PATH_ZALO_OA);
    }
}
