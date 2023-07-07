<?php
namespace Rollpix\Elebar\Test\Unit\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Rollpix\Elebar\Block\Adminhtml\System\Config\CallbackStatus;
use Rollpix\Elebar\Model\CredentialsValidator;

class CallbackStatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CredentialsValidator
     */
    private $credentialsValidatorMock;
    /**
     * @var AbstractElement|\PHPUnit\Framework\MockObject\MockObject
     */
    private $abstractElementMock;
    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;
    /**
     * @var CallbackStatus
     */
    private $callbackStatus;

    protected function setUp(): void
    {
        $this->abstractElementMock = $this->createMock(AbstractElement::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->credentialsValidatorMock = $this->createMock(CredentialsValidator::class);
        $this->callbackStatus = new CallbackStatus($this->contextMock,$this->credentialsValidatorMock,[]);
    }

    public function testGetElementHtmlOnUserAuthenticated(){
        $expected = sprintf('<div class="control-value"><span class="success">%s</span></div>',__('Callback Registered.'));
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            CallbackStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $this->credentialsValidatorMock->method('validateCallback')->willReturn(CredentialsValidator::USER_AUTHENTICATED);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->callbackStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testGetElementHtmlOnIncompleteCredentials(){
        $expected = sprintf('<div class="control-value"><span class="warning">%s</span></div>',__('Credentials section is incomplete. Please, complete the section and try again.'));
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            CallbackStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $this->credentialsValidatorMock->method('validateCallback')->willReturn(CredentialsValidator::INCOMPLETE_CREDENTIALS);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->callbackStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testGetElementHtmlOnCallbackNotEquals(){
        $expected = sprintf('<div class="control-value"><span class="warning">%s</span></div>',__('The URL registered is different than declared in the module.'));
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            CallbackStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $this->credentialsValidatorMock->method('validateCallback')->willReturn(CredentialsValidator::CALLBACK_NOT_EQUALS);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->callbackStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testGetElementHtmlOnError(){
        $expected = sprintf('<div class="control-value"><span class="error">%s</span></div>',__('Check if your credentials are correct for validate the callback url.'));
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            CallbackStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $this->credentialsValidatorMock->method('validateCallback')->willReturn(CredentialsValidator::USER_NOT_AUTHENTICATED);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->callbackStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testDecorateRowHtml(){
        $expected = '<tr id="row_test-id" class="row_payment_other_elebar_validation_credentials">html_test</tr>';
        $_decorateRowHtmlTestMethod = new \ReflectionMethod(
            CallbackStatus::class,
            '_decorateRowHtml'
        );
        $_decorateRowHtmlTestMethod->setAccessible(true);
        $this->abstractElementMock->method('getHtmlId')->willReturn('test-id');
        $getElementHtmlResult = $_decorateRowHtmlTestMethod->invokeArgs($this->callbackStatus,[$this->abstractElementMock,'html_test']);
        $this->assertEquals($expected,$getElementHtmlResult);
    }
}
