<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 10/13/16
 * Time: 19:01
 */

namespace Magenest\MapList\Block\Adminhtml\Map\Edit\Tab\AddLocation\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Store\Model\StoreManagerInterface;

class Image extends AbstractRenderer
{
    private $zoomLevel = 20;
    private $size = 100;
    private $full_size = 500;
    private $mapType = 'roadmap';
    private $markerColor = 'red';
    private $mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?';
    private $markerLabel = "";
    private $apiKey;
    private $_storeManager;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        StoreManagerInterface $storemanager,
        array $data = array()
    ) {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }

    private function getStaticUrl($lat, $lng, $size)
    {
        return $this->mapUrl . "center=" . $lat . "," . $lng . "&zoom=" . $this->zoomLevel
            . "&size=" . $size . "x" . $size . "&maptype=" . $this->mapType
            . "&markers=color:" . $this->markerColor . "%7Clabel:" . $this->markerLabel . "%7C"
            . $lat . "," . $lng . "&key=" . $this->apiKey;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $alt = $row['title'];
        $link = $this->getStaticUrl($row['latitude'], $row['longitude'], $this->size);
        $fullSizeLink = $this->getStaticUrl($row['latitude'], $row['longitude'], $this->full_size);

        return '<img id="location_map" org_src="' . $fullSizeLink . '" src="' . $link . '" alt="' . $alt . '"/>';
    }
}
