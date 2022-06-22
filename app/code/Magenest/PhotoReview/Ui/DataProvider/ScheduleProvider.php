<?php
namespace Magenest\PhotoReview\Ui\DataProvider;

class ScheduleProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /*
     * Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $collectionFactory
     */
    protected $collection;

    public function __construct(
        \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ){
        $this->collection = $this->setCollection($collectionFactory);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    private function setCollection($collectionFactory)
    {
        return $collectionFactory->create()->addFieldToFilter('job_code',['in' => ['magenest_send_email_review_reminder']]);
    }
}