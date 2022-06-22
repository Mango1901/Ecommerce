<?php
declare(strict_types=1);

namespace Magenest\CustomAsyncClient\Model;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\AsyncClient;
use Magento\Framework\HTTP\AsyncClientInterface;

/**
 * @internal
 */
class GuzzleAsyncCallback implements AsyncClientInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param callable $callback
     *
     * @return GuzzleAsyncCallback
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function request(AsyncClient\Request $request): AsyncClient\HttpResponseDeferredInterface
    {
        if (!$this->callback) {
            throw new LocalizedException(__("A callback is required to submit this request."));
        }
        $options                          = [];
        $options[RequestOptions::HEADERS] = $request->getHeaders();
        if ($request->getBody() !== null) {
            $options[RequestOptions::BODY] = $request->getBody();
        }

        return new GuzzleCallbackDeferred(
            $this->client->requestAsync($request->getMethod(), $request->getUrl(), $options),
            $this->callback
        );
    }
}
