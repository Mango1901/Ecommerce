<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 21/09/2016
 * Time: 15:01
 */

namespace Magenest\MapList\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magenest\MapList\Model\ResourceModel\HolidayLocation\Collection;

/**
 * Class Logo
 *
 * @package Magenest\MapList\Ui\Component\Listing\Columns
 */
class NoOfLocation extends Column
{
    protected $_holidayLocationFactory;
    protected $_holiday;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Collection  $holidayLocation,
        array $components = array(),
        array $data = array()
    ) {
        $this->_holidayLocation = $holidayLocation;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item){
                $locationList = $this->_holidayLocation->getItemsByColumnValue('holiday_id', $item['holiday_id']);
                    $item['holiday_number'] = 'Applied ' . count($locationList) . ' stores' ;
            }
        }
        return $dataSource;

    }
}
