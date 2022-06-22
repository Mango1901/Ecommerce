<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Router\ActionList;
use Magento\Framework\App\Route\ConfigInterface;
use Magento\Framework\App\Router\PathConfigInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Authorization
 * @package Magenest\OrderManagement\Helper
 */
class Authorization extends AbstractHelper
{
    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var ActionList
     */
    protected $_actionList;

    /**
     * @var ConfigInterface
     */
    protected $_routeConfig;

    /**
     * @var PathConfigInterface
     */
    protected $_pathConfig;

    /**
     * @var UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var string
     */
    protected $_pathPrefix = FrontNameResolver::AREA_CODE;

    /**
     * @var string
     */
    protected $_actionInterface = ActionInterface::class;

    /**
     * @var array
     */
    protected $_requiredParams = ['moduleFrontName', 'actionPath', 'actionName'];

    /**
     * Constructor.
     *
     * @param Context $context
     * @param AuthorizationInterface $authorization
     * @param ActionList $actionList
     * @param ConfigInterface $routeConfig
     * @param PathConfigInterface $pathConfig
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        Context $context,
        AuthorizationInterface $authorization,
        ActionList $actionList,
        ConfigInterface $routeConfig,
        PathConfigInterface $pathConfig,
        UrlInterface $backendUrl
    )
    {
        $this->_authorization = $authorization;
        $this->_actionList = $actionList;
        $this->_routeConfig = $routeConfig;
        $this->_pathConfig = $pathConfig;
        $this->_backendUrl = $backendUrl;
        parent::__construct($context);
    }

    /**
     * Allow access to url
     *
     * @param string $url
     * @param bool $isBaseUrlExist
     * @return bool
     */
    public function allowAccessToUrl($url, $isBaseUrlExist = true)
    {
        $resource = $this->_getAdminResourceByUrl($url, $isBaseUrlExist);

        return $this->_authorization->isAllowed($resource);
    }

    /**
     * Get admin resource by url
     *
     * @param string $url
     * @param bool $isBaseUrlExist
     * @return mixed|null
     */
    private function _getAdminResourceByUrl($url, $isBaseUrlExist = true)
    {
        $params = $this->_parseRequest($url, $isBaseUrlExist);
        /** Searching router args by module name from route using it as key */
        $modules = $this->_routeConfig->getModulesByFrontName($params['moduleFrontName']);
        if (empty($modules)) {
            return null;
        }

        foreach ($modules as $moduleName) {
            $actionClassName = $this->_actionList->get($moduleName, $this->_pathPrefix, $params['actionPath'], $params['actionName']);
            if (!$actionClassName || !is_subclass_of($actionClassName, $this->_actionInterface)) {
                continue;
            }

            break;
        }

        if (isset($actionClassName) && !empty($actionClassName)) {
            try {
                $instance = new \ReflectionClass($actionClassName);

                return $instance->getConstant("ADMIN_RESOURCE");
            } catch (\Exception $e) {
                $this->_logger->critical($e->getMessage());
            }
        }

        return null;
    }

    /**
     * Parse request URL params
     *
     * @param string $urlPath
     * @param boolean $isBaseUrlExist
     * @return array
     */
    protected function _parseRequest($urlPath, $isBaseUrlExist)
    {
        if ($isBaseUrlExist) {
            $baseUrl = $this->_urlBuilder->getBaseUrl() . $this->_backendUrl->getAreaFrontName();
            $urlPath = substr($urlPath, strlen($baseUrl));
        }

        $output = [];
        $path = trim($urlPath, '/');
        $params = explode('/', $path ? $path : $this->_pathConfig->getDefaultPath());

        foreach ($this->_requiredParams as $paramName) {
            $output[$paramName] = array_shift($params);
        }

        for ($i = 0, $l = sizeof($params); $i < $l; $i += 2) {
            $output['variables'][$params[$i]] = isset($params[$i + 1]) ? urldecode($params[$i + 1]) : '';
        }

        return $output;
    }
}
