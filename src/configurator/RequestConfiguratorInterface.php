<?php

declare(strict_types=1);

namespace Codenixsv\ApiClient\Configurator;

use Psr\Http\Message\RequestInterface;

/**
 * Interface RequestConfiguratorInterface
 * @package Codenixsv\ApiClient
 */
interface RequestConfiguratorInterface
{
    public function configure(RequestInterface $request): RequestInterface;
}
