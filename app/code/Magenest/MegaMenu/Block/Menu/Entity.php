<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Block\Menu;

/**
 * Class Entity
 * @package Magenest\MegaMenu\Block\Menu
 *
 * @method \Magenest\MegaMenu\Block\Menu\Entity|null getParentMenu()
 */
class Entity extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Element\BlockInterface
{
	protected $_subTemplate = "Magenest_MegaMenu::menu/entity/submenu.phtml";

	protected $_topTemplate = "Magenest_MegaMenu::menu/entity/topmenu.phtml";

	protected $_fileStorage;

	protected $_confHelper;

	protected $_topData;

	protected $_categoryFactory;

    private $_nestedLevel = 0;

    /**
     * Entity constructor.
     *
     * @param \Magento\MediaStorage\Model\File\Storage\File $fileStorage
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\MegaMenu\Helper\DefaultConfig $defaultConfig
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
	public function __construct(
		\Magento\MediaStorage\Model\File\Storage\File $fileStorage,
		\Magento\Framework\View\Element\Template\Context $context,
		\Magenest\MegaMenu\Helper\DefaultConfig $defaultConfig,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->_confHelper = $defaultConfig;
		$this->_fileStorage = $fileStorage;
		$this->_categoryFactory = $categoryFactory;
	}

	public function getTemplate()
	{
		$top = $this->getTop();

		if (!isset($top['title'])) {
			return parent::getTemplate();
		}

		return isset($top['level']) ? $this->_subTemplate : $this->_topTemplate;
	}

	public function getTop()
	{
		if (!isset($this->_topData)) {
			$defTop = $this->_initTop();
			$cusTop = $this->getData('top');
			if (!is_array($cusTop)) {
				$cusTop = [];
			}
			if (($cusTop['itemEnable'] ?? false) == 1) {
                $cusTop = array_filter($cusTop, function ($item) {
                    if ($item == "" || is_null($item)) {
                        return false;
                    }

                    return true;
                });
                $top = array_merge($defTop, $cusTop);
                $this->parseTopLink($top);
                $this->_topData = $top;
            }
		}
		return $this->_topData;
	}

	private function _initTop()
	{
		return $this->_confHelper->getValuesAsArr();
	}

	private function parseTopLink(array &$top)
	{
		$index = 'link';
		if (!isset($top[$index])) {
			return;
		}
		if (!preg_match('/^(http:\/\/|https:\/\/)/', $top[$index])) {
			$baseUrl = rtrim($this->getBaseUrl(), '/');
			$top[$index] = rtrim($top[$index], '/');
			$top[$index] = "{$baseUrl}/{$top[$index]}";
		}
	}

	public function getMenuData()
	{
		$top = $this->getTop();

		return [
			$top,
			$this->getData('effect'),
			$this->getData('helper'),
			$this->getData('classes')
		];
	}

	public function getIconBlock()
	{
		$iconData = $this->getTopVar('icon', []);
		if (!is_array($iconData)) {
			$iconData = [];
		}

		$img = $iconData['name'] ?? "";
		if ($img) {
			$img = $this->getImageRelativePath($img);
			$iconData['url'] = preg_replace("/{$iconData['name']}$/", $img, $iconData['url']);
		}

		return $this->getLayout()->createBlock(
			\Magenest\MegaMenu\Block\Menu\Component\Icon::class,
			'',
			[
				'data' => $iconData
			]
		);
	}

	private function getTopVar($var, $default = null)
	{
		$top = $this->getTop();
		if (is_array($top) && isset($top[$var])) {
			return $top[$var];
		}

		return $default;
	}

	protected function getImageRelativePath($img)
	{
		if (!is_string($img)) {
			return $img;
		}
		$mediaBaseDir = $this->_fileStorage->getMediaBaseDirectory();
		$isFileExists = file_exists($mediaBaseDir . '/mega_menu/item/' . $img);
		if (!$isFileExists) {
			$img = 'tmp/' . $img;
		}

		return $img;
	}

	public function getBackgroundBlock()
	{
		return $this->getLayout()->createBlock(
			\Magenest\MegaMenu\Block\Menu\Component\Background::class,
			'',
			[
				'data' => $this->getBackgroundData()
			]
		);
	}

	private function getBackgroundData()
	{
		$top = $this->getTop();

		// backgroundImage is used in frontend, background_image is used in preview mode
		$image = $top['backgroundImage'] ?? ($top['background_image'] ?? "");
		if ($image && is_string($image)) {
			$image = $this->getImageRelativePath($image);
		}

		return [
			'image' => $image,
			'color' => $top['backgroundColor'] ?? "",
			'background-repeat' => $top['backgroundRepeat'] ?? "",
			'opacity' => $top['backgroundOpacity'] ?? ""
		];
	}

	public function getLabelBlock()
	{
		return $this->getLayout()->createBlock(
			\Magenest\MegaMenu\Block\Menu\Component\Label::class,
			'',
			[
				'data' => $this->getLabelData()
			]
		);
	}

	private function getLabelData()
	{
		$top = $this->getTop();

		return [
			'label' => $top['label'] ?? "",
		];
	}

	public function getSideBlock($position)
	{
		return $this->getLayout()->createBlock(
			\Magenest\MegaMenu\Block\Menu\Component\Side::class,
			'',
			[
				'data' => $this->getSideData($position)
			]
		);
	}

	private function getSideData($position)
	{
		$return = ['position' => $position];
		$varData = ['enable', 'class', 'contentHtml', 'width'];
		foreach ($varData as $varDatum) {
			$return[$varDatum] = $this->getTopVar($position . ucfirst($varDatum));
		}

		return $return;
	}

	public function isParentTab()
	{
		if (!$parent = $this->getParentMenu()) {
			return false;
		}

		return !$parent->getTopVar('level') && $parent->getTopVar('mainContentType') === 'tabs';
	}

	public function emulateSubCategory($parentCat)
    {
        $result = [];
        if (empty($parentCat)) {
            return $result;
        }
        $category = $this->_categoryFactory->create()->load($parentCat);
        $allChild = $category->getChildrenCategories();
        foreach ($allChild as $child) {
            $this->_nestedLevel = 0;
            $result[] = $this->createVirtualChild($child);
        }

        return $result;
    }

    private function createVirtualChild(\Magento\Catalog\Model\Category $child)
    {
        $this->_nestedLevel++;
        $childrens = [];
        if ($this->_nestedLevel < 2 && !empty($child->getChildrenCategories()->getItems())) {
            foreach ($child->getChildrenCategories()->getItems() as $children) {
                $childrens[] = $this->createVirtualChild($children);
            }
        }

        return [
            'title' => $child->getName(),
            'level' => 1,
            'sort' => 10,
            'label' => 0,
            'mainContentType' => "content",
            'mainColumn' => 4,
            'mainContentHtml' => null,
            'mainEnable' => 0,
            'leftEnable' => 0,
            'rightEnable' => 0,
            'link' => $child->getUrl(),
			'categoryId' => $child->getId(),
            'footerEnable' => 0,
            'headerEnable' => 0,
            'animationIn' => 1,
            'itemEnable' => 1,
            'linkTarget' => "_self",
            'hideText' => 0,
            'hasChild' => empty($childrens) ? "no" : "yes",
            'childrens' => $childrens,
			'obj_id' => $child->getData('obj_id')
        ];
    }
}
