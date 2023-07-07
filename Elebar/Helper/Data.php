<?php

namespace Rollpix\Elebar\Helper;

use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\StoreManagerInterface;
use Rollpix\Elebar\Service\ApiService as RollpixService;

class Data extends \Magento\Payment\Helper\Data
{
    const GENERAL_SECTION = 'payment/elebar_gateway/';
    const CHECKOUT_SECTION = 'payment/elebar_gateway/checkout/';
    const CREDENTIAL_SECTION = 'payment/elebar_gateway/credentials/';
    const DEBUG_SECTION = 'payment/elebar_gateway/debug/';

    const USER_AUTHENTICATED = 1;
    const INCOMPLETE_CREDENTIALS = 0;
    const USER_NOT_AUTHENTICATED = -1;
    const CALLBACK_NOT_EQUALS = 2;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        LayoutFactory $layoutFactory,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\App\Config\Initial $initialConfig,
        StoreManagerInterface $storeManager,
        \Rollpix\Elebar\Logger\Logger $logger,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        parent::__construct($context, $layoutFactory, $paymentMethodFactory, $appEmulation, $paymentConfig, $initialConfig);
        $this->storeManager = $storeManager;
        $this->elebarLogger = $logger;
        $this->encryptor = $encryptor;
    }

    public function isDebugEnabled(){
        return $this->getConfigFlag(
            self::GENERAL_SECTION . 'debug_mode'
        );
    }

    public function isActive(){
        return $this->getConfigFlag(self::GENERAL_SECTION . 'active');
    }
    public function validateCategory(){
        return $this->getConfigFlag(self::GENERAL_SECTION . 'validate_category');
    }
    public function getCategoriesEnableRollpix(){
        return $this->getConfigData(self::GENERAL_SECTION . 'categories_enable_elebar');
    }
    public function getTitle(){
        return $this->getConfigData(self::GENERAL_SECTION . 'checkout_title');
    }

    public function getDescription(){
        return $this->getConfigData(self::GENERAL_SECTION . 'checkout_description');
    }

    public function getNewOrderStatus(){
        return $this->getConfigData(self::GENERAL_SECTION . 'order_status');
    }

    public function getApprovedOrderStatus(){
        return $this->getConfigData(self::GENERAL_SECTION . 'approved_order_status');
    }

    public function getFailureOrderStatus(){
        return $this->getConfigData(self::GENERAL_SECTION . 'failure_order_status');
    }

    public function canCancelOnFailure(){
        return $this->getConfigFlag(self::GENERAL_SECTION . 'cancel_order');
    }

    public function getClientID(){
        return "XXX".$this->getConfigData(self::GENERAL_SECTION . 'clientid');
    }

    public function getClientSecret(){
        return $this->encryptor->decrypt($this->getConfigData(self::GENERAL_SECTION . 'clientsecret'));
    }

    public function getClientStoreId($storeId =null){
        return $this->getConfigData(self::GENERAL_SECTION . 'storeid',$storeId);
    }

    public function getCallbackUrl(): string
    {
        return $this->storeManager->getDefaultStoreView()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . 'rest/default/V1/elebar/callback';
    }

    public function getCurrentCurrencyCode(){
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    public function getServiceUrl(){
        return $this->getConfigFlag(self::GENERAL_SECTION . 'sanbox_mode') ? 'https://merchants.preprod.playdigital.com.ar' : 'https://merchants.playdigital.com.ar';
    }
    public function getScriptUrl(){
        return $this->getConfigFlag(self::GENERAL_SECTION . 'sanbox_mode') ? 'https://ecommerce-modal.preprod.elebar.com.ar/bundle.js' : 'https://ecommerce-modal.elebar.com.ar/bundle.js';
    }

    private function getConfigFlag($path,$storeId=null){
        if($storeId==null){
        return $this->scopeConfig->isSetFlag(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE

        );
        }
        else
            return $this->scopeConfig->isSetFlag(
                $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    private function getConfigData($path,$storeId=null){
        if($storeId==null){
               return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        }
        else
            return $this->scopeConfig->getValue(
                $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId
            );
    }

    public function log($message)
    {
        if($this->isDebugEnabled()) {
            $this->elebarLogger->setName('elebar_payments.log');
            $this->elebarLogger->info($message);
        }
        return true;
    }
}
