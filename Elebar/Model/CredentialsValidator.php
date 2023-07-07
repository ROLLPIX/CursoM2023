<?php

namespace Rollpix\Elebar\Model;

use Rollpix\Elebar\Service\ApiService as RollpixService;
use Rollpix\Elebar\Helper\Data as RollpixHelper;

class CredentialsValidator
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

    public function __construct(
        RollpixHelper  $elebarHelper,
        RollpixService $elebarService
    )
    {
        $this->elebarHelper = $elebarHelper;
        $this->elebarService = $elebarService;
    }

    /**
     * @return int
     */
    public function validateCredentials(): int
    {
        $result = self::USER_NOT_AUTHENTICATED;
        if($this->elebarHelper->getClientID() != '' && $this->elebarHelper->getClientSecret() != '' && ($clientStoreId = $this->elebarHelper->getClientStoreId()) != ''){
            try {
                if($merchantInformation = $this->elebarService->getMerchantInformation()){
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
                if($merchantInformation = $this->elebarService->getMerchantInformation()){
                    if($merchantInformation['callback'] !== $this->elebarHelper->getCallbackUrl()){
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
        return $this->validateCallback() == self::USER_AUTHENTICATED && $this->elebarHelper->getCurrentCurrencyCode() === 'ARS';
    }
}
