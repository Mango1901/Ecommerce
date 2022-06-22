<?php
declare(strict_types=1);

namespace Magenest\MapList\Model\DistanceProvider\Goong;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\InventoryDistanceBasedSourceSelection\Model\Convert\LatLngToQueryString;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterface;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\GetDistanceInterface;

/**
 * @inheritdoc
 */
class GetDistance implements GetDistanceInterface
{
    private const GOONG_ENDPOINT = 'https://rsapi.goong.io/DistanceMatrix';
    private const XML_PATH_MODE = 'cataloginventory/source_selection_distance_based_goong/mode';
    private const XML_PATH_VALUE = 'cataloginventory/source_selection_distance_based_goong/value';

    /**
     * @var array
     */
    private $distanceCache = [];

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var LatLngToQueryString
     */
    private $latLngToQueryString;

    /**
     * @var GetApiKey
     */
    private $getApiKey;

    /**
     * GetLatLngFromAddress constructor.
     *
     * @param ClientInterface $client
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $json
     * @param LatLngToQueryString $latLngToQueryString
     * @param GetApiKey $getApiKey
     */
    public function __construct(
        ClientInterface $client,
        ScopeConfigInterface $scopeConfig,
        Json $json,
        LatLngToQueryString $latLngToQueryString,
        GetApiKey $getApiKey
    ) {
        $this->client = $client;
        $this->json = $json;
        $this->scopeConfig = $scopeConfig;
        $this->latLngToQueryString = $latLngToQueryString;
        $this->getApiKey = $getApiKey;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(LatLngInterface $source, LatLngInterface $destination): float
    {
        $sourceString = $this->latLngToQueryString->execute($source);
        $destinationString =  $this->latLngToQueryString->execute($destination);

        $key = $sourceString . '|' . $destinationString;

        if (!isset($this->distanceCache[$key])) {
            $queryString = http_build_query([
                'api_key' => $this->getApiKey->execute(),
                'origins' => $sourceString,
                'destinations' => $destinationString,
                'vehicle' => $this->scopeConfig->getValue(self::XML_PATH_MODE),
            ]);

            $this->client->get(self::GOONG_ENDPOINT . '?' . $queryString);
            if ($this->client->getStatus() !== 200) {
                throw new LocalizedException(__('Unable to connect goong API for distance matrix'));
            }

            $res = $this->json->unserialize($this->client->getBody());

            if (!isset($res['rows'][0]['elements'][0]['status']) || $res['rows'][0]['elements'][0]['status'] !== 'OK') {
                throw new LocalizedException(
                    __(
                        'Unable to get distance between %1 and %2',
                        $sourceString,
                        $destinationString
                    )
                );
            }

            $element = $res['rows'][0]['elements'][0];

            if ($this->scopeConfig->getValue(self::XML_PATH_VALUE) === 'duration') {
                $this->distanceCache[$key] = (float)$element['duration']['value'];
            } else {
                $this->distanceCache[$key] = (float)$element['distance']['value'];
            }
        }

        return $this->distanceCache[$key];
    }
}
