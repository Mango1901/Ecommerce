<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 16/04/2016
 * Time: 21:49
 */

namespace Magenest\MapList\Observer\Topmenu;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\UrlInterface;

class Add implements ObserverInterface
{
    protected $_urlInterface;

    public function __construct(
        UrlInterface $urlInterface,
        \Magenest\MapList\Helper\Config $mapConfig
    ) {
        $this->_urlInterface = $urlInterface;
        $this->mapConfig = $mapConfig;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $url = $this->_urlInterface->getUrl('maplist');

        $data = array(
            'name' => __('Stores'),
            'id' => 'maplist_topmenu',
            'url' => $url,
            'is_active' => 0
        );

        $node = new Node($data, 'id', $tree, $menu);
        if ($this->mapConfig->isShowMapListItem()) {
            $menu->addChild($node);
        }

        return $this;
    }
}
