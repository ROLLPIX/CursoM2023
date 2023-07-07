<?php
namespace Rollpix\Elebar\Test\Unit\Block\Adminhtml\System\Config;


use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Rollpix\Elebar\Model\CredentialsValidator;
use Rollpix\Elebar\Block\Adminhtml\System\Config\CredentialsStatus;

class CredentialsStatusTest extends \PHPUnit\Framework\TestCase
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
     * @var CredentialsStatus
     */
    private $credentialsStatus;

    protected function setUp(): void
    {
        $this->abstractElementMock = $this->createMock(AbstractElement::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->credentialsValidatorMock = $this->createMock(CredentialsValidator::class);
        $this->credentialsStatus = new CredentialsStatus($this->contextMock,$this->credentialsValidatorMock,[]);
    }

    public function testGetElementHtmlOnUserAuthenticated(){
        $expected = sprintf('<div class="control-value"><span class="success">%s</span></div>',__('User authenticated.'));
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            CredentialsStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $this->credentialsValidatorMock->method('validateCredentials')->willReturn(CredentialsValidator::USER_AUTHENTICATED);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->credentialsStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testGetElementHtmlOnIncompleteCredentials(){
        $expected = sprintf('<div class="control-value"><span class="warning">%s</span></div>',__('Credentials section is incomplete. Please, complete the section and try again.'));
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            CredentialsStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $this->credentialsValidatorMock->method('validateCredentials')->willReturn(CredentialsValidator::INCOMPLETE_CREDENTIALS);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->credentialsStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testGetElementHtmlOnError(){
        $expected = sprintf('<div class="control-value"><span class="error">%s</span></div>',__('User not authenticated.'));
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            CredentialsStatus::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $this->credentialsValidatorMock->method('validateCredentials')->willReturn(CredentialsValidator::USER_NOT_AUTHENTICATED);
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->credentialsStatus,[$this->abstractElementMock]);
        $this->assertEquals($expected,$getElementHtmlResult);
    }

    public function testDecorateRowHtml(){
        $expected = '<tr id="row_test-id" class="row_payment_other_elebar_validation_credentials">html_test</tr>';
        $_decorateRowHtmlTestMethod = new \ReflectionMethod(
            CredentialsStatus::class,
            '_decorateRowHtml'
        );
        $_decorateRowHtmlTestMethod->setAccessible(true);
        $this->abstractElementMock->method('getHtmlId')->willReturn('test-id');
        $getElementHtmlResult = $_decorateRowHtmlTestMethod->invokeArgs($this->credentialsStatus,[$this->abstractElementMock,'html_test']);
        $this->assertEquals($expected,$getElementHtmlResult);
    }
}