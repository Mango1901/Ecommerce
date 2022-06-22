<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_tn233 extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\Cms\Plugin\Model;

use Magento\Widget\Model\Widget as BaseWidget;

class WidgetPlugin
{
    /**
     * @var array
     */
    protected $pramsImage  = ['photo_title_mobile','photo_title_desktop'];

    public function beforeGetWidgetDeclaration(BaseWidget $subject, $type, $params = [], $asIs = true)
    {
        foreach ($this->pramsImage as $pi) {
            if (key_exists($pi, $params)) {
                $url = $params[$pi];
                if (strpos($url, '/directive/___directive/') !== false) {
                    $parts = explode('/', $url);
                    $key   = array_search("___directive", $parts);
                    if ($key !== false) {
                        $url = $parts[$key + 1];
                        $url = base64_decode(strtr($url, '-_,', '+/='));

                        $parts = explode('"', $url);
                        $key   = array_search("{{media url=", $parts);
                        $url   = $parts[$key + 1];

                        $params[$pi] = $url;
                    }
                }
            }
        }

        return [$type, $params, $asIs];
    }
}
