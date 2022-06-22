<?php
/**
 * @copyright Copyright (c) Magenet (https://www.m.com)
 */

namespace Magenest\OrderManagement\Plugin;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
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
        if ($component->getName() != 'sales_order_grid') {
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
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = (int)$dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
            foreach ($items as $item) {
                $item->setData('customer_group', $this->getGroupNameByGroupId((int)$item->getData('customer_group')));
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

    private function getGroupNameByGroupId($groupId)
    {
        $objectManager = ObjectManager::getInstance();
        $groupRepository = $objectManager->create('\Magento\Customer\Api\GroupRepositoryInterface');
        try {
            $group = $groupRepository->getById($groupId);
        } catch (\Exception $exception){
            return $groupId;
        }
        return $group->getCode();
    }
}
