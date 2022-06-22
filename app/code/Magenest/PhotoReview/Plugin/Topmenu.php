<?php
namespace Magenest\PhotoReview\Plugin;

use Magenest\PhotoReview\Helper\Data;

class Topmenu
{
    /** @var \Magenest\PhotoReview\Helper\Data  */
    protected $_helperData;

    /** @var \Magento\Framework\UrlInterface  */
    protected $_urlInterface;

    /** @var \Magento\Framework\App\Request\Http  */
    protected $_request;

    public function __construct(
        \Magenest\PhotoReview\Helper\Data $helperData,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Request\Http $http
    ){
        $this->_helperData = $helperData;
        $this->_urlInterface = $urlInterface;
        $this->_request = $http;
    }
    public function afterGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $html
    ){
        $isActiveMenu = (boolean)$this->_helperData->getScopeConfig(Data::ADD_LINK_TOPMENU);
        $isEnableModule = (boolean)$this->_helperData->isModuleEnable();
        if($isActiveMenu && $isEnableModule){
            $title = $this->_helperData->getScopeConfig(Data::MENU_TITLE);
            $reviewGalleryUrl = $subject->getUrl('photoreview/gallery');
            $menuTitle = "" ? __('Review Gallery') : $title;
            $magentoCurrentUrl = $subject->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
            if (strpos($magentoCurrentUrl,'photoreview/gallery') !== false) {
                $html .= "<li class=\"level0 nav-5 active level-top ui-menu-item\">";
            } else {
                $html .= "<li class=\"level0 nav-4 level-top parent ui-menu-item\">";
            }
            $html .= "<a href=\"" . $reviewGalleryUrl . "\" class=\"level-top ui-corner-all\"><span>" . $menuTitle . "</span></a>";
            $html .= "</li>";
        }
        return $html;
    }
}