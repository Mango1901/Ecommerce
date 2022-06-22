<?php

namespace Magenest\Cms\Plugin\Model;

/**
 * Class Page
 */
class Page
{
    /**
     * Match with any text start 'data-note="' and end stop at first match '"'
     */
    const REGEX_REPLACE_ATTRIBUTE = '/data-note=\"([^\"]*)\"/';

    /**
     * @param \Magento\Cms\Model\Page $subject
     * @param $result
     * @return string
     */
    public function afterGetContent(\Magento\Cms\Model\Page $subject, $result)
    {
        return preg_replace(self::REGEX_REPLACE_ATTRIBUTE, '', $result);
    }
}
