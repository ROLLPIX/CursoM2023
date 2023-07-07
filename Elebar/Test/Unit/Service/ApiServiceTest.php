<?php

namespace Rollpix\Elebar\Test\Unit\Service;

use Rollpix\Elebar\Service\ApiService as RollpixService;
use Rollpix\Elebar\Helper\Data as RollpixHelper;

class ApiServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RollpixService
     */
    private $elebarService;

    /**
     * @var \Rollpix\Elebar\Helper\Data|\PHPUnit\Framework\MockObject\MockObject
     */
    private $elebarHelperMock;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheMock;

    protected function setUp(): void
    {
        $this->elebarHelperMock = $this->createMock(RollpixHelper::class);
        $this->elebarHelperMock->method('getServiceUrl')->willReturn('https://merchants.preprod.playdigital.com.ar');
        $this->cacheMock = $this->createMock(\Magento\Framework\App\Cache\Type\Config::class);
        $this->cacheMock->method('save')->willReturnSelf();

        $this->elebarService = new RollpixService($this->cacheMock, $this->elebarHelperMock);
    }

    public function testGetMerchantFromCache()
    {
        $this->cacheMock->method('test')->willReturn(true);
        $this->cacheMock->method('load')->willReturn("{\"callback\":\"prueba\",\"storesid\":\"prueba\"}");

        $getMerchantCredentialsTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Service\ApiService::class,
            'getMerchantFromCache'
        );
        $getMerchantCredentialsTestMethod->setAccessible(true);
        $result = $getMerchantCredentialsTestMethod->invoke($this->elebarService);
        $this->assertTrue(is_array($result));
    }

    public function testGetTokenFromCache()
    {
        $this->cacheMock->method('test')->willReturn(true);
        $this->cacheMock->method('load')->willReturn("test");

        $getTokenFromCacheTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Service\ApiService::class,
            'getTokenFromCache'
        );
        $getTokenFromCacheTestMethod->setAccessible(true);
        $result = $getTokenFromCacheTestMethod->invoke($this->elebarService);
        $this->assertTrue(is_string($result));
    }

    public function testSetMerchantCredentials()
    {
        $setMerchantCredentialsTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Service\ApiService::class,
            'setMerchantCredentials'
        );
        $setMerchantCredentialsTestMethod->setAccessible(true);
        $merchant = ['merchant-test'];
        $result = $setMerchantCredentialsTestMethod->invokeArgs($this->elebarService,['merchant' => $merchant]);
        $this->assertEquals($merchant,$result);
    }

    public function testSetAuthorizationToken()
    {
        $setAuthorizationTokenTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Service\ApiService::class,
            'setAuthorizationToken'
        );
        $setAuthorizationTokenTestMethod->setAccessible(true);
        $authToken = 'token-test';
        $result = $setAuthorizationTokenTestMethod->invokeArgs($this->elebarService,['authToken' => $authToken]);
        $this->assertEquals($authToken,$result);
    }

    public function testGetAuthorizationUrl() {
        $getAuthorizationUrlTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Service\ApiService::class,
            'getAuthorizationUrl'
        );
        $getAuthorizationUrlTestMethod->setAccessible(true);
        $result = $getAuthorizationUrlTestMethod->invoke($this->elebarService);

        $expectedResult = 'https://merchants.preprod.playdigital.com.ar/merchants/middleman/token';
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetRegisterWebhookUrl() {

        $getRegisterWebhookUrlTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Service\ApiService::class,
            'getRegisterWebhookUrl'
        );
        $getRegisterWebhookUrlTestMethod->setAccessible(true);
        $result = $getRegisterWebhookUrlTestMethod->invoke($this->elebarService);

        $expectedResult = 'https://merchants.preprod.playdigital.com.ar/merchants/middleman';
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetPaymentIntentionCreationUrl() {

        $getPaymentIntentionCreationUrlTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Service\ApiService::class,
            'getPaymentIntentionCreationUrl'
        );
        $getPaymentIntentionCreationUrlTestMethod->setAccessible(true);
        $result = $getPaymentIntentionCreationUrlTestMethod->invoke($this->elebarService);

        $expectedResult = 'https://merchants.preprod.playdigital.com.ar/merchants/ecommerce/payment-intention';
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetMerchantUrl() {

        $getMerchantUrlTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Service\ApiService::class,
            'getMerchantUrl'
        );
        $getMerchantUrlTestMethod->setAccessible(true);
        $result = $getMerchantUrlTestMethod->invoke($this->elebarService);

        $expectedResult = 'https://merchants.preprod.playdigital.com.ar/merchants/middleman';
        $this->assertEquals($expectedResult, $result);
    }
}
