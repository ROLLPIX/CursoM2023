<?php

namespace Rollpix\Elebar\Service;

use Magento\Framework\App\Cache\Type\Config as Cache;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\AuthenticationException;
use Rollpix\Elebar\Model\CredentialsValidator;

class ApiService
{
    const TOKEN_CACHE_KEY = 'elebar_api_token';
    const USER_CACHE_KEY = 'elebar_api_user';

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var RollpixHelper
     */
    private $elebarHelper;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var array
     */
    private $merchant;

    public function __construct
    (
        Cache $cacheManager,
        RollpixHelper $elebarHelper
    )
    {
        $this->cache = $cacheManager;
        $this->elebarHelper = $elebarHelper;
    }

    /**
     * @param $orderNumber
     * @param $productsQty
     * @param $total
     * @return array
     * @throws LocalizedException
     */
    public function createPaymentIntention($storeId,$orderNumber, $productsQty, $total){
        $response = [];
        if($authToken = $this->getAuthorizationToken()) {
            $response = $this->request(
                $this->getPaymentIntentionCreationUrl(),

                json_encode(['productName' => 'Compra desde magento',
                            'price' => $total,
                            'quantity' => $productsQty,
                            'storeId' => $this->elebarHelper->getClientStoreId($storeId),

                            'externalIntentionId' => $orderNumber,
                            'currency' => 'ARS']),
                ['User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201','Content-Type: application/json','Authorization: Bearer ' . $authToken]
            );
        }

        return $response;
    }

    /**
     * @param $callbackUrl
     * @return array
     * @throws LocalizedException
     */
    public function registerWebhook($callbackUrl): array
    {
        $response = [];
        if($authToken = $this->getAuthorizationToken()) {
            $response = $this->request(
                $this->getRegisterWebhookUrl(),
                json_encode(['callbackUrl' => $callbackUrl]),
                ['User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201','Content-Type: application/json','Authorization: Bearer ' . $authToken],
                'PATCH'
            );
        }
        return $response;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getMerchantInformation(): array
    {
        $merchant = $this->merchant ?: $this->getMerchantFromCache();
        if(!$merchant && $authToken = $this->getAuthorizationToken()) {
            $response = $this->request($this->getMerchantUrl(), null, ['User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201','Content-Type: application/json', 'Authorization: Bearer ' . $authToken], 'GET');
            if (isset($response['stores'])) {
                $storesId = [];
                foreach ($response['stores'] as $store){
                    if (isset($store['id'])){
                        $storesId[] = $store['id'];
                    }
                }
                $merchant['storesid'] = $storesId;
            }
            if (isset($response['callbackUrl'])) {
                if ($response['callbackUrl'] != $this->elebarHelper->getCallbackUrl()){
                  $this->registerWebhook($this->elebarHelper->getCallbackUrl());
                }
            }
            else
            {
                $this->registerWebhook($this->elebarHelper->getCallbackUrl());
            }
            $merchant['callback'] = $this->elebarHelper->getCallbackUrl();
            $this->setMerchantCredentials($merchant);
        }
        return $merchant;
    }

    /**
     * @param $orderNumber
     * @param $productsQty
     * @param $total
     * @return array
     * @throws LocalizedException
     */
    public function getPaymentIntention($pId){
        $response = [];
        if($authToken = $this->getAuthorizationToken()) {
            $response = $this->request(
                $this->getPaymentIntentionCreationUrl() . '/'.$pId
                ,null
                ,['User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201','Content-Type: application/json', 'Authorization: Bearer ' . $authToken], 'GET'
            );
        }

        return $response;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getAuthorizationToken(): string
    {
        $authToken = $this->accessToken?: $this->getTokenFromCache();
        if(!$authToken){
            $authToken = $this->createToken();
        }
        return $authToken;
    }

    /**
     * @return array
     */
    private function getMerchantFromCache(): array
    {
        $merchant = [];
        if ($this->cache->test(self::USER_CACHE_KEY)) {
            $merchantCache = json_decode($this->cache->load(self::USER_CACHE_KEY),true);
            if(isset($merchantCache['callback']) && isset($merchantCache['storesid']))
            {
                $merchant = $this->setMerchantCredentials($merchantCache);
            }
        }
        return $merchant;
    }

    private function getTokenFromCache(): string
    {
        $authToken = '';
        if ($this->cache->test(self::TOKEN_CACHE_KEY)) {
            $authToken = $this->cache->load(self::TOKEN_CACHE_KEY);
            $authToken = $this->setAuthorizationToken($authToken);
        }
        return $authToken;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    private function createToken()
    {
        try {
            $response = $this->request($this->getAuthorizationUrl(), json_encode(['username' => $this->elebarHelper->getClientID(),'password' => $this->elebarHelper->getClientSecret()]), ['Content-Type: application/json','User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201']);
            if (!isset($response['accessToken'])) {
                throw new AuthenticationException(__('No token returned'));
            }
            $authToken = $this->setAuthorizationToken($response['accessToken']);
        } catch (LocalizedException $e) {
            throw new AuthenticationException(__('Unable to retrieve Rollpix API token. ' . $e->getMessage()));
        }
        return $authToken;
    }

    /**
     * @param array $merchant
     */
    private function setMerchantCredentials($merchant){
        $this->merchant = $merchant;
        $this->cache->save(json_encode($merchant), self::USER_CACHE_KEY, [],10000);
        return $merchant;
    }

    /**
     * @param string
     */
    private function setAuthorizationToken($authToken): string
    {
        $this->accessToken = $authToken;
        $this->cache->save($authToken, self::TOKEN_CACHE_KEY, [],10000);
        return $this->accessToken;
    }

    /**
     * @return string
     */
    private function getAuthorizationUrl(): string
    {
        return $this->elebarHelper->getServiceUrl() . '/merchants/middleman/token';
    }

    /**
     * @return string
     */
    private function getPaymentIntentionCreationUrl(): string
    {
        return $this->elebarHelper->getServiceUrl() . '/merchants/ecommerce/payment-intention';
    }

    private function getRegisterWebhookUrl():string
    {
        return $this->elebarHelper->getServiceUrl() . '/merchants/middleman';
    }

    private function getMerchantUrl(): string
    {
        return $this->elebarHelper->getServiceUrl() . '/merchants/middleman';
    }

    /**
     * @param $url
     * @param $body
     * @param array $headers
     * @param string $method
     * @return array
     * @throws LocalizedException
     */
    function request($url, $body, $headers = [], $method = 'POST')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $this->elebarHelper->log("From: \Rollpix\Elebar\Service\ApiService::request\nURL: $url\nMETHOD: $method\nREQUEST: $body\nRESPONSE:$response");

        if (!$response) {
            throw new LocalizedException(__('No response from request to ' . $url));
        }

        if (!empty($error)) {
            throw new LocalizedException(__('Error returned with request to ' . $url . '. Error: ' . $error));
        }

        return json_decode($response,true);
    }
}
