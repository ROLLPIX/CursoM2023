<?php


namespace Modo\Gateway\Test\Unit\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Backend\Block\Template\Context;
use Modo\Gateway\Block\Adminhtml\System\Config\Disabled;

class DisabledTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Disabled
     */
    private $disableMock;
    /**
     * @var AbstractElement
     */
    private $abstractElementMock;

    protected function setUp(): void
    {
        $this->abstractElementMock = $this->createMock(AbstractElement::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->secureHtmlRenderer = $this->createMock(SecureHtmlRenderer::class);

        $this->disableMock = $this->createMock(\Modo\Gateway\Block\Adminhtml\System\Config\Disabled::class);
    }

    public function testGetElementHtml(){
        $_getElementHtmlTestMethod = new \ReflectionMethod(
            Disabled::class,
            '_getElementHtml'
        );
        $_getElementHtmlTestMethod->setAccessible(true);
        $this->abstractElementMock->method('getElementHtml')->willReturn('html-test');
        $getElementHtmlResult = $_getElementHtmlTestMethod->invokeArgs($this->disableMock,[$this->abstractElementMock]);
        $this->assertTrue(is_string($getElementHtmlResult));
    }
}
