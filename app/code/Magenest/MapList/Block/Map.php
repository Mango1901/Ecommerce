<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/14/16
 * Time: 13:31
 */

namespace Magenest\MapList\Block;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory as SourceCollectionFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class Map extends Block
{
    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var SourceCollectionFactory
     */
    private $sourceCollectionFactory;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        \Magenest\MapList\Model\Holiday $holiday,
        \Magenest\MapList\Model\ResourceModel\HolidayLocation\Collection $holidayLoction,
        \Magenest\MapList\Model\Config\Source\Options $options,
        \Magento\Framework\Registry $registry, \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        Json $json,
        SourceCollectionFactory $sourceCollectionFactory,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->sourceCollectionFactory = $sourceCollectionFactory;
        parent::__construct($context, $locationFactory, $holiday, $holidayLoction, $options, $registry, $country, $storeManager, $timezoneInterface, $json);
    }

    public function getMap()
    {
        $mapData = [
            'currentMenu' => 'map',
            'config' => $this->getConfig(),
        ];

        try {
            $searchCriteria = $this->getSearchCriteria();
            $sources = $this->sourceRepository->getList($searchCriteria);
            $data = [];
            foreach ($sources->getItems() as $key => $source) {
                if ($source->getData('source_code') == "default" || $source->getData('visible') == "0" ) {
                    continue;
                }
                $detailAddress = implode(', ', [$source->getStreet(), $source->getWard(), $source->getDistrict(), $source->getCity()]);
                $source->setData('detail_address', $detailAddress);
                try {
                    $storeMapImage = $this->_json->unserialize($source->getStoreMapImg());
                    $source->setStoreMapImg($storeMapImage[0]);
                } catch (\Throwable $e){
                    $this->_logger->error($e->getMessage());
                }

                $data[] = $source->getData();
            }

            $mapData['location'] = $data;
            return $mapData;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getSearchCriteria()
    {
        return $this->searchCriteriaBuilder
            ->addFilter('enabled', 1)
            ->addFilter('is_visible', 0, 'neq')
            ->addFilter('source_code', "default", 'neq')
            ->create();
    }

    public function getImageLogoStore()
    {
        return $this->getViewFileUrl('Magenest_MapList::images/logo-hoangphuc.png');
    }

    public function countEnabledSource(){
        $sourceCollection = $this->sourceCollectionFactory->create();
        return $sourceCollection->addFieldToFilter("visible",1)->count();
    }
}
