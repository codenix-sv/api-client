<?php

declare(strict_types=1);

namespace Codenixsv\ApiClient;

use Codenixsv\ApiClient\Configurator\RequestConfiguratorInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface BaseClientInterface
 * @package Codenixsv\ApiClient
 */
interface BaseClientInterface
{
    /**
     * @return string
     */
    public function getBaseUri(): string;

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri): void;

    /**
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface;

    /**
     * @param RequestConfiguratorInterface $configurator
     */
    public function addRequestConfigurator(RequestConfiguratorInterface $configurator): void;

    /**
     * @return RequestConfiguratorInterface[]
     */
    public function getRequestConfigurators(): array;

    /**
     * @return void
     */
    public function clearRequestConfigurators(): void;

    /**
     * @param string $endpoint
     * @param array $headers
     * @return ResponseInterface
     */
    public function get(string $endpoint, array $headers = []): ResponseInterface;

    /**
     * @param string $endpoint
     * @param $body
     * @param array $headers
     * @return ResponseInterface
     */
    public function post(string $endpoint, $body, array $headers = []): ResponseInterface;

    /**
     * @param string $endpoint
     * @param $body
     * @param array $headers
     * @return ResponseInterface
     */
    public function patch(string $endpoint, $body, array $headers = []): ResponseInterface;

    /**
     * @param string $endpoint
     * @param array $headers
     * @return ResponseInterface
     */
    public function delete(string $endpoint, array $headers = []): ResponseInterface;
}
