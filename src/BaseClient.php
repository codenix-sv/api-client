<?php

declare(strict_types=1);

namespace Codenixsv\ApiClient;

use Codenixsv\ApiClient\Configurator\RequestConfiguratorInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Class BaseClient
 * @package Codenixsv\ApiClient
 */
class BaseClient implements BaseClientInterface
{
    /** @var ClientInterface  */
    private $httpClient;
    /** @var RequestFactoryInterface  */
    private $requestFactory;
    /** @var UriFactoryInterface  */
    private $uriFactory;
    /** @var StreamFactoryInterface  */
    private $streamFactory;
    /** @var string  */
    private $baseUri = '';
    /** @var RequestConfiguratorInterface[] */
    private $requestConfigurators = [];

    public function __construct(
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?UriFactoryInterface $uriFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->httpClient = $httpClient ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->uriFactory = $uriFactory ?: Psr17FactoryDiscovery::findUrlFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri): void
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param RequestConfiguratorInterface $configurator
     */
    public function addRequestConfigurator(RequestConfiguratorInterface $configurator): void
    {
        $this->requestConfigurators[] = $configurator;
    }

    /**
     * @return void
     */
    public function clearRequestConfigurators(): void
    {
        $this->requestConfigurators = [];
    }

    /**
     * @return RequestConfiguratorInterface[]
     */
    public function getRequestConfigurators(): array
    {
        return $this->requestConfigurators;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param null|string|StreamInterface|resource $body
     * @param array $headers
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function request(string $method, string $endpoint, $body = null, array $headers = []): ResponseInterface
    {
        $request = $this->buildRequest($method, $endpoint, $body, $headers);
        return $this->httpClient->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    private function configureRequest(RequestInterface $request): RequestInterface
    {
        foreach ($this->requestConfigurators as $configurator) {
            $request = $configurator->configure($request);
        }
        return $request;
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function get(string $endpoint, array $headers = []): ResponseInterface
    {
        return $this->request('GET', $endpoint, null, $headers);
    }

    /**
     * @param string $endpoint
     * @param $body
     * @param array $headers
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function post(string $endpoint, $body, array $headers = []): ResponseInterface
    {
        return $this->request('POST', $endpoint, $body, $headers);
    }

    /**
     * @param string $endpoint
     * @param $body
     * @param array $headers
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function patch(string $endpoint, $body, array $headers = []): ResponseInterface
    {
        return $this->request('PATCH', $endpoint, $body, $headers);
    }

    /**
     * @param string $endpoint
     * @param $body
     * @param array $headers
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function put(string $endpoint, $body, array $headers = []): ResponseInterface
    {
        return $this->request('PUT', $endpoint, $body, $headers);
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function delete(string $endpoint, array $headers = []): ResponseInterface
    {
        return $this->request('DELETE', $endpoint, null, $headers);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param $body
     * @param array $headers
     * @return RequestInterface
     */
    protected function buildRequest(
        string $method,
        string $endpoint,
        $body = null,
        array $headers = []
    ): RequestInterface {
        $uri = $this->uriFactory->createUri($this->getBaseUri() . $endpoint);

        $request = $this->requestFactory->createRequest($method, $uri);
        $request = $this->appendBodyToRequest($request, $body);
        $request = $this->appendHeadersToRequest($request, $headers);
        $request = $this->configureRequest($request);

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @param array $headers
     * @return RequestInterface
     */
    protected function appendHeadersToRequest(RequestInterface $request, array $headers)
    {
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        return $request;
    }

    /**
     * @param RequestInterface $request
     * @param $body
     * @return RequestInterface
     */
    protected function appendBodyToRequest(RequestInterface $request, $body)
    {
        if ($body !== null) {
            if (is_resource($body)) {
                $body = $this->streamFactory->createStreamFromResource($body);
            }
            if (!($body instanceof StreamInterface)) {
                $body = $this->streamFactory->createStream((string) $body);
            }
            $request = $request->withBody($body);
        }

        return $request;
    }
}
