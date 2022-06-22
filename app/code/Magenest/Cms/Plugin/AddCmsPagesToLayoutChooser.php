<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 30/11/2021
 * Time: 10:23
 */

namespace Magenest\Cms\Plugin;


use Magento\Cms\Model\Config\Source\Page;
use Magento\Framework\DataObject;
use Magento\Framework\View\Layout\PageType\Config;

class AddCmsPagesToLayoutChooser
{
    protected $cmsPages;

    public function __construct(Page $page)
    {
        $this->cmsPages = $page;
    }

    /**
     * @param \Magento\Framework\View\Layout\PageType\Config $subject
     * @param $result
     */
    public function afterGetPageTypes(\Magento\Framework\View\Layout\PageType\Config $subject, $result)
    {
        $key = "cms_page_view_id_";
        foreach ($this->cmsPages->toOptionArray() as $page) {
            $id = $key.$page['value'];
            $result[$id] = new DataObject([
                'id' => $id,
                'label' => "CMS Page - " . $page['label']
            ]);
        }
        return $result;
    }
}
