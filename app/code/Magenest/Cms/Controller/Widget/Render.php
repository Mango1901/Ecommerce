<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 30/11/2021
 * Time: 16:24
 */

namespace Magenest\Cms\Controller\Widget;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Serialize\Serializer\Base64Json;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Layout\LayoutCacheKeyInterface;
use Magento\PageCache\Controller\Block;
use Magento\Widget\Model\Template\Filter;

class Render extends Block implements HttpGetActionInterface
{
    protected $filter;

    public function __construct(
        Context $context,
        InlineInterface $translateInline,
        Filter $filter,
        Json $jsonSerializer = null,
        Base64Json $base64jsonSerializer = null,
        LayoutCacheKeyInterface $layoutCacheKey = null
    ) {
        $this->filter = $filter;
        parent::__construct(
            $context,
            $translateInline,
            $jsonSerializer,
            $base64jsonSerializer,
            $layoutCacheKey
        );
    }

    /**
     * Returns block content depends on ajax request
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return;
        }
        // disable profiling during private content handling AJAX call
        \Magento\Framework\Profiler::reset();
        $widgetData = $this->getRequest()->getParams();
        $widgetDataStr = "";
        foreach ($widgetData as $key => $value) {
            if (is_string($value)) {
                $widgetDataStr .= $key . "=\"" . $value . "\" ";
            }
        }
        $widgetHtml = $this->filter->generateWidget([
            $widgetData,
            'widget',
            $widgetDataStr
        ]);

        $this->getResponse()->setPrivateHeaders(\Magento\PageCache\Helper\Data::PRIVATE_MAX_AGE_CACHE);
        $this->translateInline->processResponseBody($widgetHtml);
        $this->getResponse()->appendBody(json_encode($widgetHtml));
    }
}
