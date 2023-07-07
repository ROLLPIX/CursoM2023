<?php

namespace Rollpix\Elebar\Test\Unit\Helper;

use \Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Rollpix\Elebar\Helper\Data as RollpixHelper;

class DataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RollpixHelper
     */
    private $model;
    /**
     * @var ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $scopeConfigMock;
    /**
     * @var \Rollpix\Elebar\Logger\Logger|\PHPUnit\Framework\MockObject\MockObject
     */
    private $elebarLogger;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $encrypterMock;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $this->storeMock = $this->createMock(\Magento\Store\Model\Store::class);
        $this->currencyMock = $this->createMock(\Magento\Directory\Model\Currency::class);
        $this->storeMock->method('getCurrentCurrency')->willReturn($this->currencyMock);
        $this->storeMock->method('getBaseUrl')->with(\Magento\Framework\UrlInterface::URL_TYPE_WEB)->willReturn('url-base-test/');
        $this->storeManagerMock->method('getStore')->willReturn($this->storeMock);

        $layoutFactoryMock = $this->createMock(LayoutFactory::class);
        $methodFactoryMock = $this->createMock(\Magento\Payment\Model\Method\Factory::class);
        $emulationMock = $this->createMock(\Magento\Store\Model\App\Emulation::class);
        $configMock = $this->createMock( \Magento\Payment\Model\Config::class);
        $initialConfigMock = $this->createMock(\Magento\Framework\App\Config\Initial::class);
        $this->elebarLogger = $this->createMock(\Rollpix\Elebar\Logger\Logger::class);
        $this->encrypterMock = $this->createMock(\Magento\Framework\Encryption\EncryptorInterface::class);

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);


        $this->model = new RollpixHelper(
            $this->contextMock,
            $layoutFactoryMock,
            $methodFactoryMock,
            $emulationMock,
            $configMock,
            $initialConfigMock,
            $this->storeManagerMock,
            $this->elebarLogger,
            $this->encrypterMock
        );
    }

    public function testIsDebugEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(RollpixHelper::GENERAL_SECTION . 'debug_mode')
            ->will($this->returnValue(true));
        $this->assertTrue((bool)$this->model->isDebugEnabled());
    }

    public function testIsActive(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(RollpixHelper::GENERAL_SECTION . 'active')
            ->will($this->returnValue(true));
        $this->assertTrue((bool)$this->model->isActive());
    }

    public function testGetTitle(): void
    {
        $expected = 'Elebar';
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(RollpixHelper::GENERAL_SECTION . 'checkout_title')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected,$this->model->getTitle());
    }

    public function testGetDescription(): void
    {
        $expected = 'Paga en un click con la app de Elebar';
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(RollpixHelper::GENERAL_SECTION . 'checkout_description')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected,$this->model->getDescription());
    }

    public function testGetNewOrderStatus(): void
    {
        $expected = 'pending';
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(RollpixHelper::GENERAL_SECTION . 'order_status')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected,$this->model->getNewOrderStatus());
    }

    public function testGetApprovedOrderStatus(): void
    {
        $expected = 'processing';
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(RollpixHelper::GENERAL_SECTION . 'approved_order_status')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected,$this->model->getApprovedOrderStatus());
    }

    public function testGetFailureOrderStatus(): void
    {
        $expected = 'canceled';
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(RollpixHelper::GENERAL_SECTION . 'failure_order_status')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected,$this->model->getFailureOrderStatus());
    }

    public function testCanCancelOnFailure(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(RollpixHelper::GENERAL_SECTION . 'cancel_order')
            ->will($this->returnValue(true));
        $this->assertTrue($this->model->canCancelOnFailure());
    }

    public function testGetClientID(): void
    {
        $expected = 'clientid-test';
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(RollpixHelper::GENERAL_SECTION . 'clientid')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected,$this->model->getClientID());
    }

    public function testGetClientSecret(): void
    {
        $expected = 'clientsecret-test';
        $this->encrypterMock->method('decrypt')->willReturn($expected);
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(RollpixHelper::GENERAL_SECTION . 'clientsecret')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected,$this->model->getClientSecret());
    }

    public function testGetClientStoreId(): void
    {
        $expected = 'clientstoreid-test';
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(RollpixHelper::GENERAL_SECTION . 'storeid')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected,$this->model->getClientStoreId());
    }

    public function testGetCallbackUrl(){
        $expected = 'url-base-test/rest/default/V1/elebar/callback';
        $this->assertEquals($expected,$this->model->getCallbackUrl());
    }

    public function testGetCurrentCurrencyCode(){
        $expected = 'ARS';
        $this->currencyMock->method('getCode')->willReturn('ARS');
        $this->assertEquals($expected,$this->model->getCurrentCurrencyCode());
    }

    public function testGetServiceUrl(){
        $expected = 'https://merchants.preprod.playdigital.com.ar';
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(RollpixHelper::GENERAL_SECTION . 'sanbox_mode')
            ->will($this->returnValue(true));
        $this->assertEquals($expected,$this->model->getServiceUrl());
    }

    public function testLog(){
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(RollpixHelper::GENERAL_SECTION . 'debug_mode')
            ->will($this->returnValue(true));
        $this->assertTrue($this->model->log('test','elebar-test.log'));
    }



    /*
    public function getClientID(){
        return $this->getConfigData(self::GENERAL_SECTION . 'clientid');
    }

    public function getClientSecret(){
        return $this->getConfigData(self::GENERAL_SECTION . 'clientsecret');
    }

    public function getClientStoreId(){
        return $this->getConfigData(self::GENERAL_SECTION . 'storeid');
    }

    public function getCallbackUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . 'rest/default/V1/elebar/callback';
    }

    public function getCurrentCurrencyCode(){
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    private function getConfigFlag($path){
        return $this->scopeConfig->isSetFlag(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getConfigData($path){

        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public static function log($message, $fileName = 'elebar.log')
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $fileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }*/
}
