<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 11/06/2022
 * Time: 10:54
 */
declare(strict_types=1);

namespace Magenest\Customer\Plugin\Controller\Adminhtml\Order;

use Magenest\Customer\Helper\ConfigHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Controller\Adminhtml\Order\AddressSave as AddressSaveMagento;
use Psr\Log\LoggerInterface;

class AddressSave
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    public function __construct(
        RequestInterface $request,
        LoggerInterface $logger,
        ConfigHelper $configHelper
    ) {
        $this->_request = $request;
        $this->logger = $logger;
        $this->configHelper = $configHelper;
    }

    /**
     * @param AddressSaveMagento $subject
     * @return array
     */
    public function beforeExecute(AddressSaveMagento $subject): array
    {
        try {
            $data = $this->_request->getPostValue();
            $isEnabledFullName = $this->configHelper->isEnabledFullNameInstead();
            if ($isEnabledFullName && !empty($data['fullname'])) {
                $nameSplit = $this->configHelper->splitFullNameToFirstLastName($data['fullname']);
                $this->_request->setPostValue('firstname', $nameSplit['firstname'] ?? '');
                $this->_request->setPostValue('lastname', $nameSplit['lastname'] ?? '');
            } else {
                if (isset($data['lastname']) && isset($data['firstname'])) {
                    $this->_request->setPostValue('fullname', $data['lastname'] . ' ' . $data['firstname']);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return [];
    }
}
