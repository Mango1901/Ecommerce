<?php

declare(strict_types=1);

namespace Magenest\MapList\Model\DistanceProvider\Goong;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Get Google API KEY
 */
class GetApiKey
{
    private const XML_PATH_API_KEY = 'cataloginventory/source_selection_distance_based_goong/api_key';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * GetApiKey constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get distance between two points
     *
     * @return string
     * @throws LocalizedException
     */
    public function execute(): string
    {
        $apiKey = trim((string) $this->scopeConfig->getValue(self::XML_PATH_API_KEY));
        if (!$apiKey) {
            throw new LocalizedException(__('Goong API key is not defined'));
        }

        return $apiKey;
    }
}
