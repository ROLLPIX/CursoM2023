<?php

namespace Modo\Gateway\Test\Unit\Model;

use Modo\Gateway\Model\CredentialsValidator;
use Modo\Gateway\Service\ApiService as ModoService;
use Modo\Gateway\Helper\Data as ModoHelper;

class CredentialsValidatorTest extends \PHPUnit\Framework\TestCase
{
    const USER_AUTHENTICATED = 1;
    const INCOMPLETE_CREDENTIALS = 0;
    const USER_NOT_AUTHENTICATED = -1;
    const CALLBACK_NOT_EQUALS = 2;

    /**
     * @var ModoHelper
     */
    private $modoHelper;
    /**
     * @var ModoService
     */
    private $modoService;
    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    protected function setUp(): void
    {
        $this->modoService = $this->getMockBuilder(ModoService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->modoHelper = $this->getMockBuilder(ModoHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->credentialsValidator = new CredentialsValidator($this->modoHelper,$this->modoService);
    }

    public function testValidateCredentialsUserAuthenticated(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->modoService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest]]);

        $credentialsValidationResult = $this->credentialsValidator->validateCredentials();

        $this->assertTrue($credentialsValidationResult === CredentialsValidator::USER_AUTHENTICATED);
    }

    public function testValidateCredentialsUserNotAuthenticated(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $editedClientStoreIdTest = 'edited-client-storeid-test';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->modoService->method('getMerchantInformation')->willReturn(['storesid' => [$editedClientStoreIdTest]]);

        $credentialsValidationResult = $this->credentialsValidator->validateCredentials();

        $this->assertTrue($credentialsValidationResult === CredentialsValidator::USER_NOT_AUTHENTICATED);
    }

    public function testValidateCredentialsIncompleteCredentials(){
        $clientIdTest = '';
        $clientSecretTest = '';
        $clientStoreIdTest = '';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);

        $credentialsValidationResult = $this->credentialsValidator->validateCredentials();

        $this->assertTrue($credentialsValidationResult === CredentialsValidator::INCOMPLETE_CREDENTIALS);
    }

    public function testValidateCallbackEquals(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->modoHelper->method('getCallbackUrl')->willReturn($merchantCallbackTest);
        $this->modoService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest],'callback' => $merchantCallbackTest]);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::USER_AUTHENTICATED);
    }

    public function testValidateCallbackNotEquals(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $editedMercantCallbackTest = 'edited-callback-test';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->modoHelper->method('getCallbackUrl')->willReturn($editedMercantCallbackTest);
        $this->modoService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest],'callback' => $merchantCallbackTest]);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::CALLBACK_NOT_EQUALS);
    }

    public function testValidateCallbackUserNotAuthenticated(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $editedClientStoreIdTest = 'edited-client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $editedMercantCallbackTest = 'edited-callback-test';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->modoHelper->method('getCallbackUrl')->willReturn($editedMercantCallbackTest);
        $this->modoService->method('getMerchantInformation')->willReturn(['storesid' => [$editedClientStoreIdTest],'callback' => $merchantCallbackTest]);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::USER_NOT_AUTHENTICATED);
    }

    public function testValidateCallbackIncompleteCredentials(){
        $clientIdTest = '';
        $clientSecretTest = '';
        $clientStoreIdTest = '';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::INCOMPLETE_CREDENTIALS);
    }

    public function testValidateCallbackLocalizedException(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test2';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->modoService->method('getMerchantInformation')->willThrowException(new \Magento\Framework\Exception\LocalizedException(__('LocalizedException Test')));

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::USER_NOT_AUTHENTICATED);
    }

    public function testValidateCallbackException(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $editedMercantCallbackTest = 'edited-callback-test';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->modoHelper->method('getCallbackUrl')->willReturn($editedMercantCallbackTest);
        $this->modoService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest],'callback2' => $merchantCallbackTest]);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::USER_NOT_AUTHENTICATED);
    }

    public function testAreCredentialsValid(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $this->modoHelper->method('getClientID')->willReturn($clientIdTest);
        $this->modoHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->modoHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->modoHelper->method('getCallbackUrl')->willReturn($merchantCallbackTest);
        $this->modoHelper->method('getCurrentCurrencyCode')->willReturn('ARS');
        $this->modoService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest],'callback' => $merchantCallbackTest]);

        $credentialsValidationResult = $this->credentialsValidator->areCredentialsValid();
        $this->assertTrue($credentialsValidationResult);
    }
}