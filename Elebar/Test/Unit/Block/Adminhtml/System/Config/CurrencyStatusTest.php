<?php


namespace Rollpix\Elebar\Test\Unit\Block\Adminhtml\System\Config;


use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Request\Http;
use Rollpix\Elebar\Block\Adminhtml\System\Config\CurrencyStatus;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CurrencyStatusTest  extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CurrencyStatus
     */
    private $currencyStatus;
    /**
     * @var AbstractElement
     */
    private $abstractElementMock;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeManagerMock;
    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;
    /**
     * @var \Magento\Store\Model\Store|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeMock;
    /**
     * @var \Magento\Directory\Model\Currency|\PHPUnit\Framework\MockObject\MockObject
     */
    private $currencyMock;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $this->storeMock = $this->createMock(\Magento\Store\Model\Store::class);
        $this->currencyMock = $this->createMock(\Magento\Directory\Model\Currency::class);
        $this->storeMock->method('getCurrentCurrency')->willReturn($this->currencyMock);
        $this->storeManagerMock->method('getStore')->willReturn($this->storeMock);
        $this->contextMock = $this->createMock(Context::class);
        $this->abstractElementMock = $this->createMock(AbstractElement::class);
        $this->contextMock->method('getStoreManager')->willReturn($this->storeManagerMock);

        $this->currencyStatus = new CurrencyStatus($this->contextMock,[]);
    }

    public function testGetElementHtmlOnSuccess(){
        $expected = '<div class="control-value"><span class="success">ARS</span></div>';
        $this->currencyMock->method('getCode')->willReturn('ARS');
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Block\Adminhtml\System\Config\CurrencyStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->currencyStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testGetElementHtmlOnError(){
        $currencyCodeTest = 'UYU';
        $expected = sprintf('<div class="control-value"><span class="error">%s - Solo transacciones en ARS estan disponibles en este momento</span></div>',$currencyCodeTest);
        $this->currencyMock->method('getCode')->willReturn($currencyCodeTest);
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Block\Adminhtml\System\Config\CurrencyStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->currencyStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testDecorateRowHtml(){
        $expected = '<tr id="row_test-id" class="row_payment_other_elebar_validation_credentials">html_test</tr>';
        $_decorateRowHtmlTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Block\Adminhtml\System\Config\CurrencyStatus::class,
            '_decorateRowHtml'
        );
        $_decorateRowHtmlTestMethod->setAccessible(true);
        $this->abstractElementMock->method('getHtmlId')->willReturn('test-id');
        $getElementHtmlResult = $_decorateRowHtmlTestMethod->invokeArgs($this->currencyStatus,[$this->abstractElementMock,'html_test']);
        $this->assertEquals($expected,$getElementHtmlResult);
    }
}
