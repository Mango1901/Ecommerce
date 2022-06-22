<?php

namespace Magenest\FastErp\Model;

use Magenest\FastErp\Api\UpdateWarehouseInformationInterface;
use Magenest\FastErp\Block\Adminhtml\Form\Field\ErpWarehouses;
use Magenest\FastErp\Model\Source\ErpWarehouse\Options;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class UpdateWarehouseInformation implements UpdateWarehouseInformationInterface
{
    /**
     * @var ClientFactory
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        ClientFactory $client,
        LoggerInterface $logger,
        WriterInterface $configWriter,
        SerializerInterface $serializer
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->configWriter = $configWriter;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    function execute()
    {
        try {
            /**
             * @var $client Client
             */
            $client = $this->client->create();

            $warehouseData = $client->getWarehouses();

            if ($warehouseData && isset($warehouseData['data'])) {
                $result = [];
                foreach ($warehouseData['data'] as $warehouse) {
                    if (
                        isset($warehouse['status']) &&
                        isset($warehouse['isDeleted']) &&
                        $warehouse['status'] &&
                        !$warehouse['isDeleted']
                    ) {
                        $result[] = [
                            ErpWarehouses::COLUMN_ERP_SOURCE_CODE => $warehouse['id'] ?? null,
                            ErpWarehouses::COLUMN_ERP_SOURCE_NAME => $warehouse['name'] ?? null
                        ];
                    }
                }
                $this->configWriter->save(Options::PATH_CONFIG, $this->serializer->serialize($result));
                return true;
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return false;
    }
}
