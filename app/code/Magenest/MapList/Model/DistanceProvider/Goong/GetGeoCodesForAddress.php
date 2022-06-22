<?php
declare(strict_types=1);

namespace Magenest\MapList\Model\DistanceProvider\Goong;

use Magenest\MapList\Helper\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\InventoryDistanceBasedSourceSelection\Model\Convert\AddressToComponentsString;
use Magento\InventoryDistanceBasedSourceSelection\Model\Convert\AddressToQueryString;
use Magento\InventoryDistanceBasedSourceSelection\Model\Convert\AddressToString;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterface;

/**
 * Get geocodes for given address service.
 */
class GetGeoCodesForAddress
{
    private const GOONG_ENDPOINT = 'https://rsapi.goong.io/geocode';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var GetApiKey
     */
    private $getApiKey;

    /**
     * @var AddressToComponentsString
     */
    private $addressToComponentsString;

    /**
     * @var AddressToString
     */
    private $addressToString;

    /**
     * @var AddressToQueryString
     */
    private $addressToQueryString;

    /**
     * @param ClientInterface $client
     * @param Json $json
     * @param GetApiKey $getApiKey
     * @param AddressToComponentsString $addressToComponentsString
     * @param AddressToQueryString $addressToQueryString
     * @param AddressToString $addressToString
     */
    public function __construct(
        ClientInterface $client,
        Json $json,
        GetApiKey $getApiKey,
        AddressToComponentsString $addressToComponentsString,
        AddressToQueryString $addressToQueryString,
        AddressToString $addressToString
    ) {
        $this->client = $client;
        $this->json = $json;
        $this->getApiKey = $getApiKey;
        $this->addressToComponentsString = $addressToComponentsString;
        $this->addressToString = $addressToString;
        $this->addressToQueryString = $addressToQueryString;
    }

    /**
     * Retrieve geocodes for given address.
     *
     * @param AddressInterface $address
     * @return array
     * @throws LocalizedException
     */
    public function execute(AddressInterface $address): array
    {
        $queryString = http_build_query([
            'address' => $this->addressToQueryString->execute($address),
            'api_key' => $this->getApiKey->execute()
        ]);

        $this->client->get(self::GOONG_ENDPOINT . '?' . $queryString);
        if ($this->client->getStatus() !== 200) {
            throw new LocalizedException(__('Unable to connect goong API for geocoding'));
        }

        $res = $this->json->unserialize($this->client->getBody());

        if ($res['status'] !== 'OK') {
            throw new LocalizedException(__('Unable to geocode address %1', $this->addressToString->execute($address)));
        }

        return $res;
    }
}
