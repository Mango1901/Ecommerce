<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 01/12/2021
 * Time: 15:51
 */

namespace Magenest\MegaMenu\Block\Widget;


use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Element\Template;

class MenuBottomMobile extends Template
{
    protected $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        Template\Context $context,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    public function getMainCategoryUrl()
    {
        $category = $this->getCategory('mega_menu/mobile_footer/main_category');
        if ($category) {
            return $category->getUrl();
        }
        return "#";
    }

    public function getPromotionCategoryUrl()
    {
        $category = $this->getCategory('mega_menu/mobile_footer/promotion_category');
        if ($category) {
            return $category->getUrl();
        }
        return "#";
    }

    protected function getCategory($path)
    {
        $categoryId = $this->_scopeConfig->getValue($path);
        try {
            return $this->categoryRepository->get($categoryId);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
