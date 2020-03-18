<?php

declare(strict_types=1);

namespace Codenixsv\ApiClient\Tests;

use Codenixsv\ApiClient\BaseClient;
use Codenixsv\ApiClient\Configurator\RequestConfiguratorInterface;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Http\Mock\Client as HttpMockClient;

class BaseClientTest extends TestCase
{
    public function testGetHttpClient()
    {
        $client = new BaseClient();
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
    }

    public function testRequest()
    {
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $httpClientMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->isInstanceOf(RequestInterface::class));

        $client = new BaseClient($httpClientMock);
        $client->request('', '');
    }

    public function testRequestWithRequestConfigurator()
    {
        $client = $this->getBaseClient();
        $configurator = $this->getMockBuilder(RequestConfiguratorInterface::class)
            ->getMock();
        $configurator->expects($this->once())
            ->method('configure')
            ->with($this->isInstanceOf(RequestInterface::class))
            ->willReturn(new Request('POST', '', ['Default' => 'header']));

        $client->addRequestConfigurator($configurator);

        $response = $client->request('POST', '');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals([
            'Default' => ['header'],
        ], $client->getHttpClient()->getLastRequest()->getHeaders());
    }

    public function testRequestWithStringAsBody()
    {
        $client = $this->getBaseClient();
        $body = 'bar=foo';

        $response = $client->post('', $body);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($body, (string) $client->getHttpClient()->getLastRequest()->getBody());
    }

    public function testRequestWithResourceAsBody()
    {
        $client = $this->getBaseClient();
        $body = fopen('php://temp', 'r+');
        $string = 'bar=foo';
        fwrite($body, $string);

        $response = $client->post('', $body);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($string, (string) $client->getHttpClient()->getLastRequest()->getBody());
    }

    public function testRequestWithStreamAsBody()
    {
        $client = $this->getBaseClient();
        $string = 'bar=foo';
        $body = Stream::create($string);

        $response = $client->post('', $body);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($string, (string) $client->getHttpClient()->getLastRequest()->getBody());
    }

    public function testPost()
    {
        $client = $this->getBaseClient();
        $endpoint = '/endpoint';
        $body = 'bar=foo';

        $response = $client->post($endpoint, $body, ['Content-Type' => 'application/json', 'Foo' => 'bar']);
        /** @var RequestInterface $lastRequest */
        $lastRequest =  $client->getHttpClient()->getLastRequest();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($body, (string) $lastRequest->getBody());
        $this->assertEquals($endpoint, $lastRequest->getUri());
        $this->assertEquals(['Content-Type' => ['application/json'], 'Foo' => ['bar']], $lastRequest->getHeaders());
    }

    public function testGet()
    {
        $client = $this->getBaseClient();
        $endpoint = '/endpoint';

        $response = $client->get($endpoint, ['Content-Type' => 'application/json', 'Foo' => 'bar']);
        /** @var RequestInterface $lastRequest */
        $lastRequest =  $client->getHttpClient()->getLastRequest();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals($endpoint, $lastRequest->getUri());
        $this->assertEquals(['Content-Type' => ['application/json'], 'Foo' => ['bar']], $lastRequest->getHeaders());
    }

    public function testDelete()
    {
        $client = $this->getBaseClient();
        $endpoint = '/endpoint';

        $response = $client->delete($endpoint, ['Content-Type' => 'application/json', 'Foo' => 'bar']);
        /** @var RequestInterface $lastRequest */
        $lastRequest =  $client->getHttpClient()->getLastRequest();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('DELETE', $lastRequest->getMethod());
        $this->assertEquals($endpoint, $lastRequest->getUri());
        $this->assertEquals(['Content-Type' => ['application/json'], 'Foo' => ['bar']], $lastRequest->getHeaders());
    }

    public function testPut()
    {
        $client = $this->getBaseClient();
        $endpoint = '/endpoint';
        $body = 'bar=foo';

        $response = $client->put($endpoint, $body, ['Content-Type' => 'application/json', 'Foo' => 'bar']);
        /** @var RequestInterface $lastRequest */
        $lastRequest =  $client->getHttpClient()->getLastRequest();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('PUT', $lastRequest->getMethod());
        $this->assertEquals($body, (string) $lastRequest->getBody());
        $this->assertEquals($endpoint, $lastRequest->getUri());
        $this->assertEquals(['Content-Type' => ['application/json'], 'Foo' => ['bar']], $lastRequest->getHeaders());
    }

    public function testPatch()
    {
        $client = $this->getBaseClient();
        $endpoint = '/endpoint';
        $body = 'bar=foo';

        $response = $client->patch($endpoint, $body, ['Content-Type' => 'application/json', 'Foo' => 'bar']);
        /** @var RequestInterface $lastRequest */
        $lastRequest =  $client->getHttpClient()->getLastRequest();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('PATCH', $lastRequest->getMethod());
        $this->assertEquals($body, (string) $lastRequest->getBody());
        $this->assertEquals($endpoint, $lastRequest->getUri());
        $this->assertEquals(['Content-Type' => ['application/json'], 'Foo' => ['bar']], $lastRequest->getHeaders());
    }

    public function testGetBaseUriWithDefaultValue()
    {
        $baseClient = new BaseClient();

        $this->assertEquals('', $baseClient->getBaseUri());
    }

    public function testSetBaseUri()
    {
        $baseClient = new BaseClient();
        $uri = 'https/test';
        $baseClient->setBaseUri($uri);

        $this->assertEquals($uri, $baseClient->getBaseUri());
    }

    public function testClearRequestConfigurators()
    {
        $baseClient = new BaseClient();
        $configurator = $this->getMockBuilder(RequestConfiguratorInterface::class)->getMock();
        $baseClient->addRequestConfigurator($configurator);

        $baseClient->clearRequestConfigurators();

        $this->assertEmpty($baseClient->getRequestConfigurators());
    }

    public function testAddRequestConfigurator()
    {
        $baseClient = new BaseClient();
        $baseClient->clearRequestConfigurators();
        $configurator = $this->getMockBuilder(RequestConfiguratorInterface::class)->getMock();

        $baseClient->addRequestConfigurator($configurator);

        $this->assertInstanceOf(RequestConfiguratorInterface::class, $baseClient->getRequestConfigurators()[0]);
    }

    /**
     * @return BaseClient
     */
    private function getBaseClient()
    {
        $httpClientMock = new HttpMockClient();
        $client = new BaseClient($httpClientMock);

        return $client;
    }
}
