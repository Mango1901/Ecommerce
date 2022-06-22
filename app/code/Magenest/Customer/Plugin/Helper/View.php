<?php

namespace Magenest\Customer\Plugin\Helper;

use Magenest\Customer\Helper\ConfigHelper;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Escaper;
use Psr\Log\LoggerInterface;

class View
{
    /**
     * @var CustomerMetadataInterface
     */
    protected $_customerMetadataService;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * Initialize dependencies.
     *
     * @param CustomerMetadataInterface $customerMetadataService
     * @param Escaper $escaper
     * @param LoggerInterface $logger
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        CustomerMetadataInterface $customerMetadataService,
        Escaper $escaper,
        LoggerInterface $logger,
        ConfigHelper $configHelper
    ) {
        $this->_customerMetadataService = $customerMetadataService;
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->configHelper = $configHelper;
    }

    /**
     * @param \Magento\Customer\Helper\View $subject
     * @param $result
     * @param CustomerInterface $customerData
     * @return array|mixed|string
     */
    public function afterGetCustomerName(\Magento\Customer\Helper\View $subject, $result, CustomerInterface $customerData)
    {
        try {
            if ($this->configHelper->isEnabledFullNameInstead()) {
                $fullName =  $customerData->getCustomAttribute('fullname')->getValue();
                return $this->escaper->escapeHtml($fullName);
            }

            $name = '';
            $prefixMetadata = $this->_customerMetadataService->getAttributeMetadata('prefix');
            if ($prefixMetadata->isVisible() && $customerData->getPrefix()) {
                $name .= $customerData->getPrefix() . ' ';
            }

            $name .= $customerData->getLastname();

            $middleNameMetadata = $this->_customerMetadataService->getAttributeMetadata('middlename');
            if ($middleNameMetadata->isVisible() && $customerData->getMiddlename()) {
                $name .= ' ' . $customerData->getMiddlename();
            }

            $name .= ' ' . $customerData->getFirstname();

            $suffixMetadata = $this->_customerMetadataService->getAttributeMetadata('suffix');
            if ($suffixMetadata->isVisible() && $customerData->getSuffix()) {
                $name .= ' ' . $customerData->getSuffix();
            }

            return $this->escaper->escapeHtml($name);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }
}
