<?php

namespace Modo\Gateway\Test\Unit\Service;

use Modo\Gateway\Service\ApiService as ModoService;
use Modo\Gateway\Helper\Data as ModoHelper;

class ApiServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ModoService
     */
    private $modoService;

    /**
     * @var \Modo\Gateway\Helper\Data|\PHPUnit\Framework\MockObject\MockObject
     */
    private $modoHelperMock;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheMock;

    protected function setUp(): void
    {
        $this->modoHelperMock = $this->createMock(ModoHelper::class);
        $this->modoHelperMock->method('getServiceUrl')->willReturn('https://merchants.preprod.playdigital.com.ar');
        $this->cacheMock = $this->createMock(\Magento\Framework\App\Cache\Type\Config::class);
        $this->cacheMock->method('save')->willReturnSelf();

        $this->modoService = new ModoService($this->cacheMock, $this->modoHelperMock);
    }

    public function testGetMerchantFromCache()
    {
        $this->cacheMock->method('test')->willReturn(true);
        $this->cacheMock->method('load')->willReturn("{\"callback\":\"prueba\",\"storesid\":\"prueba\"}");

        $getMerchantCredentialsTestMethod = new \ReflectionMethod(
            \Modo\Gateway\Service\ApiService::class,
            'getMerchantFromCache'
        );
        $getMerchantCredentialsTestMethod->setAccessible(true);
        $result = $getMerchantCredentialsTestMethod->invoke($this->modoService);
        $this->assertTrue(is_array($result));
    }

    public function testGetTokenFromCache()
    {
        $this->cacheMock->method('test')->willReturn(true);
        $this->cacheMock->method('load')->willReturn("test");

        $getTokenFromCacheTestMethod = new \ReflectionMethod(
            \Modo\Gateway\Service\ApiService::class,
            'getTokenFromCache'
        );
        $getTokenFromCacheTestMethod->setAccessible(true);
        $result = $getTokenFromCacheTestMethod->invoke($this->modoService);
        $this->assertTrue(is_string($result));
    }

    public function testSetMerchantCredentials()
    {
        $setMerchantCredentialsTestMethod = new \ReflectionMethod(
            \Modo\Gateway\Service\ApiService::class,
            'setMerchantCredentials'
        );
        $setMerchantCredentialsTestMethod->setAccessible(true);
        $merchant = ['merchant-test'];
        $result = $setMerchantCredentialsTestMethod->invokeArgs($this->modoService,['merchant' => $merchant]);
        $this->assertEquals($merchant,$result);
    }

    public function testSetAuthorizationToken()
    {
        $setAuthorizationTokenTestMethod = new \ReflectionMethod(
            \Modo\Gateway\Service\ApiService::class,
            'setAuthorizationToken'
        );
        $setAuthorizationTokenTestMethod->setAccessible(true);
        $authToken = 'token-test';
        $result = $setAuthorizationTokenTestMethod->invokeArgs($this->modoService,['authToken' => $authToken]);
        $this->assertEquals($authToken,$result);
    }

    public function testGetAuthorizationUrl() {
        $getAuthorizationUrlTestMethod = new \ReflectionMethod(
            \Modo\Gateway\Service\ApiService::class,
            'getAuthorizationUrl'
        );
        $getAuthorizationUrlTestMethod->setAccessible(true);
        $result = $getAuthorizationUrlTestMethod->invoke($this->modoService);

        $expectedResult = 'https://merchants.preprod.playdigital.com.ar/merchants/middleman/token';
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetRegisterWebhookUrl() {

        $getRegisterWebhookUrlTestMethod = new \ReflectionMethod(
            \Modo\Gateway\Service\ApiService::class,
            'getRegisterWebhookUrl'
        );
        $getRegisterWebhookUrlTestMethod->setAccessible(true);
        $result = $getRegisterWebhookUrlTestMethod->invoke($this->modoService);

        $expectedResult = 'https://merchants.preprod.playdigital.com.ar/merchants/middleman';
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetPaymentIntentionCreationUrl() {

        $getPaymentIntentionCreationUrlTestMethod = new \ReflectionMethod(
            \Modo\Gateway\Service\ApiService::class,
            'getPaymentIntentionCreationUrl'
        );
        $getPaymentIntentionCreationUrlTestMethod->setAccessible(true);
        $result = $getPaymentIntentionCreationUrlTestMethod->invoke($this->modoService);

        $expectedResult = 'https://merchants.preprod.playdigital.com.ar/merchants/ecommerce/payment-intention';
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetMerchantUrl() {

        $getMerchantUrlTestMethod = new \ReflectionMethod(
            \Modo\Gateway\Service\ApiService::class,
            'getMerchantUrl'
        );
        $getMerchantUrlTestMethod->setAccessible(true);
        $result = $getMerchantUrlTestMethod->invoke($this->modoService);

        $expectedResult = 'https://merchants.preprod.playdigital.com.ar/merchants/middleman';
        $this->assertEquals($expectedResult, $result);
    }
}
