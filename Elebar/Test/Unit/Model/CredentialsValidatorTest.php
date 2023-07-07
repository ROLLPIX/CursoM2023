<?php

namespace Rollpix\Elebar\Test\Unit\Model;

use Rollpix\Elebar\Model\CredentialsValidator;
use Rollpix\Elebar\Service\ApiService as RollpixService;
use Rollpix\Elebar\Helper\Data as RollpixHelper;

class CredentialsValidatorTest extends \PHPUnit\Framework\TestCase
{
    const USER_AUTHENTICATED = 1;
    const INCOMPLETE_CREDENTIALS = 0;
    const USER_NOT_AUTHENTICATED = -1;
    const CALLBACK_NOT_EQUALS = 2;

    /**
     * @var RollpixHelper
     */
    private $elebarHelper;
    /**
     * @var RollpixService
     */
    private $elebarService;
    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    protected function setUp(): void
    {
        $this->elebarService = $this->getMockBuilder(RollpixService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->elebarHelper = $this->getMockBuilder(RollpixHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->credentialsValidator = new CredentialsValidator($this->elebarHelper,$this->elebarService);
    }

    public function testValidateCredentialsUserAuthenticated(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->elebarService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest]]);

        $credentialsValidationResult = $this->credentialsValidator->validateCredentials();

        $this->assertTrue($credentialsValidationResult === CredentialsValidator::USER_AUTHENTICATED);
    }

    public function testValidateCredentialsUserNotAuthenticated(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $editedClientStoreIdTest = 'edited-client-storeid-test';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->elebarService->method('getMerchantInformation')->willReturn(['storesid' => [$editedClientStoreIdTest]]);

        $credentialsValidationResult = $this->credentialsValidator->validateCredentials();

        $this->assertTrue($credentialsValidationResult === CredentialsValidator::USER_NOT_AUTHENTICATED);
    }

    public function testValidateCredentialsIncompleteCredentials(){
        $clientIdTest = '';
        $clientSecretTest = '';
        $clientStoreIdTest = '';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);

        $credentialsValidationResult = $this->credentialsValidator->validateCredentials();

        $this->assertTrue($credentialsValidationResult === CredentialsValidator::INCOMPLETE_CREDENTIALS);
    }

    public function testValidateCallbackEquals(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->elebarHelper->method('getCallbackUrl')->willReturn($merchantCallbackTest);
        $this->elebarService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest],'callback' => $merchantCallbackTest]);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::USER_AUTHENTICATED);
    }

    public function testValidateCallbackNotEquals(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $editedMercantCallbackTest = 'edited-callback-test';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->elebarHelper->method('getCallbackUrl')->willReturn($editedMercantCallbackTest);
        $this->elebarService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest],'callback' => $merchantCallbackTest]);

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
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->elebarHelper->method('getCallbackUrl')->willReturn($editedMercantCallbackTest);
        $this->elebarService->method('getMerchantInformation')->willReturn(['storesid' => [$editedClientStoreIdTest],'callback' => $merchantCallbackTest]);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::USER_NOT_AUTHENTICATED);
    }

    public function testValidateCallbackIncompleteCredentials(){
        $clientIdTest = '';
        $clientSecretTest = '';
        $clientStoreIdTest = '';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::INCOMPLETE_CREDENTIALS);
    }

    public function testValidateCallbackLocalizedException(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test2';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->elebarService->method('getMerchantInformation')->willThrowException(new \Magento\Framework\Exception\LocalizedException(__('LocalizedException Test')));

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::USER_NOT_AUTHENTICATED);
    }

    public function testValidateCallbackException(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $editedMercantCallbackTest = 'edited-callback-test';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->elebarHelper->method('getCallbackUrl')->willReturn($editedMercantCallbackTest);
        $this->elebarService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest],'callback2' => $merchantCallbackTest]);

        $callbackValidationResult = $this->credentialsValidator->validateCallback();
        $this->assertTrue($callbackValidationResult === CredentialsValidator::USER_NOT_AUTHENTICATED);
    }

    public function testAreCredentialsValid(){
        $clientIdTest = 'client-id-test';
        $clientSecretTest = 'client-secret-test';
        $clientStoreIdTest = 'client-storeid-test';
        $merchantCallbackTest = 'callback-test';
        $this->elebarHelper->method('getClientID')->willReturn($clientIdTest);
        $this->elebarHelper->method('getClientSecret')->willReturn($clientSecretTest);
        $this->elebarHelper->method('getClientStoreId')->willReturn($clientStoreIdTest);
        $this->elebarHelper->method('getCallbackUrl')->willReturn($merchantCallbackTest);
        $this->elebarHelper->method('getCurrentCurrencyCode')->willReturn('ARS');
        $this->elebarService->method('getMerchantInformation')->willReturn(['storesid' => [$clientStoreIdTest],'callback' => $merchantCallbackTest]);

        $credentialsValidationResult = $this->credentialsValidator->areCredentialsValid();
        $this->assertTrue($credentialsValidationResult);
    }
}