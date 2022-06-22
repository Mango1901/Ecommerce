<?php
declare(strict_types=1);

namespace Magenest\CustomAsyncClient\Model;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\CancellationException;
use GuzzleHttp\Promise\PromiseInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Async\CancelingDeferredException;
use Magento\Framework\HTTP\AsyncClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Wrapper around Guzzle's response promise. Executing a callback after request successfully.
 * @internal
 */
class GuzzleCallbackDeferred implements AsyncClient\HttpResponseDeferredInterface
{
    /**
     * @var PromiseInterface
     */
    private $promise;

    /**
     * @var AsyncClient\Response
     */
    private $response;

    /**
     * @var AsyncClient\HttpException
     */
    private $exception;

    /**
     * @var bool
     */
    private $canceled = false;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PromiseInterface $promise
     * @param callable $callback
     * @param LoggerInterface|null $logger
     */
    public function __construct(PromiseInterface $promise, callable $callback, LoggerInterface $logger = null)
    {
        $this->logger   = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->promise  = $promise;
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function isDone(): bool
    {
        return $this->response || $this->exception;
    }

    /**
     * Convert guzzle response to Magento response.
     *
     * @param ResponseInterface $response
     *
     * @return AsyncClient\Response
     */
    private function convertResponse(ResponseInterface $response): AsyncClient\Response
    {
        /** @var string[] $headers */
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }
        return new AsyncClient\Response($response->getStatusCode(), $headers, $response->getBody()->getContents());
    }

    /**
     * Unwrap guzzle's promise.
     */
    private function unwrap(): void
    {
        try {
            /** @var ResponseInterface $response */
            $response       = $this->promise->wait();
            $this->response = $this->convertResponse($response);
        } catch (RequestException $requestException) {
            if ($requestException instanceof BadResponseException) {
                $this->response = $this->convertResponse($requestException->getResponse());
            } else {
                $this->exception = new AsyncClient\HttpException(
                    $requestException->getMessage(),
                    0,
                    $requestException
                );
            }
        } catch (\Throwable $exception) {
            $this->exception = $exception;
        }
    }

    /**
     * @return void
     */
    private function executeCallback()
    {
        if (!$this->exception) {
            try {
                ($this->callback)($this->response);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function get(): AsyncClient\Response
    {
        if ($this->isCancelled()) {
            throw new CancelingDeferredException('Deferred is canceled');
        }
        if (!$this->isDone()) {
            $this->unwrap();
            $this->executeCallback();
        }

        if ($this->exception) {
            throw $this->exception;
        }
        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function cancel(bool $force = false): void
    {
        if ($force) {
            $this->promise->cancel();
            if ($this->promise->getState() === PromiseInterface::REJECTED) {
                $this->unwrap();
                if ($this->exception instanceof CancellationException) {
                    $this->canceled = true;
                    return;
                }
            }
        }

        throw new CancelingDeferredException('Failed to cancel HTTP request');
    }

    /**
     * @inheritDoc
     */
    public function isCancelled(): bool
    {
        return $this->canceled;
    }
}
