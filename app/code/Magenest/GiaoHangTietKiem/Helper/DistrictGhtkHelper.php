<?php
namespace Magenest\GiaoHangTietKiem\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Dir\Reader;
use Zend\Config\Reader\Json;

class DistrictGhtkHelper extends AbstractHelper
{
    /**
     * @var Json
     */
    private $jsonReader;

    /**
     * @var Reader
     */
    private $moduleReader;

    private $cities;

    public function __construct(
        Context $context,
        Reader $moduleReader,
        Json $jsonReader
    ) {
        $this->jsonReader = $jsonReader;
        $this->moduleReader = $moduleReader;
        parent::__construct($context);
        $this->initData();
    }

    private function initData()
    {
        $directory = $this->moduleReader->getModuleDir('view', 'Magenest_GiaoHangTietKiem');
        $jsonFile = $directory."/frontend/web/json/address_converted.json";
        $this->cities = $this->jsonReader->fromFile($jsonFile);
    }

    public function getDistrictData()
    {
        if ($this->cities == null) {
            $this->initData();
        }
        return $this->cities;
    }
}
