<?php

namespace Magenest\Customer\Plugin\Model\Metadata;

use Exception;
use Magenest\Customer\Helper\ConfigHelper;
use Magento\Customer\Model\Metadata\Form as FormMagento;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class Form
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ConfigHelper $configHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigHelper    $configHelper,
        LoggerInterface $logger
    )
    {
        $this->configHelper = $configHelper;
        $this->logger = $logger;
    }

    /**
     * @param FormMagento $subject
     * @param array $result
     * @param RequestInterface $request
     * @param string|null $scope
     * @param bool $scopeOnly
     * @return array
     */
    public function afterExtractData(
        FormMagento      $subject,
        array            $result,
        RequestInterface $request,
                         $scope = null,
                         $scopeOnly = true
    ) {
        try {
            $isEnabledFullName = $this->configHelper->isEnabledFullNameInstead();
            if ($isEnabledFullName && !empty($result['fullname'])) {
                $nameSplit = $this->configHelper->splitFullNameToFirstLastName($result['fullname']);
                $result = array_merge($result, $nameSplit);
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return $result;
    }
}
