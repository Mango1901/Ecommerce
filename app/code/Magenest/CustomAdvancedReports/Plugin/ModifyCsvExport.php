<?php
/**
 * @copyright Copyright (c) Magenet (https://www.m.com)
 */

namespace Magenest\CustomAdvancedReports\Plugin;

use Exception;
use Magenest\Directory\Model\ResourceModel\City;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Model\Export\ConvertToCsv;

/**
 * Class ConvertToCsv
 * @package DrinkiesLocal\OrderManagement\Plugin
 */
class ModifyCsvExport extends ConvertToCsv
{
    /**
     * @param ConvertToCsv $subject
     * @param callable $proceed
     * @return array
     * @throws LocalizedException
     * @throws FileSystemException
     */
    public function aroundGetCsvFile(
        ConvertToCsv $subject,
        callable $proceed
    ) {
        $component = $subject->filter->getComponent();
        if (!in_array($component->getName(), ['aw_arep_category_grid','aw_arep_salesdetailed_grid'])) {
            return $proceed();
        }
        $name = hash('sha256', microtime());
        $file = 'export/' . $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        if ($component->getName() == 'aw_arep_category_grid') {
            $key = array_search('category', $fields);
            $header = $this->metadataProvider->getHeaders($component);
            $keyHeader = array_search('Name', $header);
            unset($fields[$key]);
            unset($header[$keyHeader]);
            $stream->writeCsv($header);
        } else {
            $stream->writeCsv($this->metadataProvider->getHeaders($component));
        }
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = (int)$dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
            foreach ($items as $item) {
                if ($component->getName() == 'aw_arep_salesdetailed_grid') {
                    $province = $this->getProvince((int)$item->getData('order_id'));
                    if ($province) {
                        $item->setData('city', $province);
                    }
                }
                $this->metadataProvider->convertDate($item, $component->getName());
                $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    private function getProvince($orderId)
    {
        $objectManager = ObjectManager::getInstance();
        /* @var OrderRepositoryInterface $orderInterface*/
        $orderInterface = $objectManager->create(OrderRepositoryInterface::class);
        try {
            $order = $orderInterface->get($orderId);
            $address = $order->getShippingAddress();
            $cityId           = $address->getCityId();
            if ($cityId && $city = $this->getCity($cityId)) {
                return $city;
            }
        } catch (Exception $exception) {
            return false;
        }
        return false;
    }
    private function getCity($cityId)
    {
        $cityResource = ObjectManager::getInstance()->create(City::class);
        return $cityResource->getDefaultNameById($cityId);
    }
}
