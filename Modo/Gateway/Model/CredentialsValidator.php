<?php

namespace Modo\Gateway\Model;

use Modo\Gateway\Service\ApiService as ModoService;
use Modo\Gateway\Helper\Data as ModoHelper;

class CredentialsValidator
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

    public function __construct(
        ModoHelper  $modoHelper,
        ModoService $modoService
    )
    {
        $this->modoHelper = $modoHelper;
        $this->modoService = $modoService;
    }

    /**
     * @return int
     */
    public function validateCredentials(): int
    {
        $result = self::USER_NOT_AUTHENTICATED;
        if($this->modoHelper->getClientID() != '' && $this->modoHelper->getClientSecret() != '' && ($clientStoreId = $this->modoHelper->getClientStoreId()) != ''){
            try {
                if($merchantInformation = $this->modoService->getMerchantInformation()){
                    if(in_array($clientStoreId,$merchantInformation['storesid'])) {
                        $result = self::USER_AUTHENTICATED;
                    }
                }
            }catch (\Magento\Framework\Exception\LocalizedException $localizedException){
            }
        }
        else{
            $result = self::INCOMPLETE_CREDENTIALS;
        }
        return $result;
    }

    /**
     * @return int
     */
    public function validateCallback(): int
    {
        $validationResult = $this->validateCredentials();
        if($validationResult == self::USER_AUTHENTICATED){
            try {
                if($merchantInformation = $this->modoService->getMerchantInformation()){
                    if($merchantInformation['callback'] !== $this->modoHelper->getCallbackUrl()){
                        $validationResult = self::CALLBACK_NOT_EQUALS;
                    }
                }
            }catch (\Exception $exception){
                $validationResult = self::USER_NOT_AUTHENTICATED;
            }
        }
        return $validationResult;
    }

    public function areCredentialsValid(): bool
    {
        return $this->validateCallback() == self::USER_AUTHENTICATED && $this->modoHelper->getCurrentCurrencyCode() === 'ARS';
    }
}
