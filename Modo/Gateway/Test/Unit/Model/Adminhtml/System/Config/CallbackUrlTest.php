<?php


namespace Modo\Gateway\Test\Unit\Model\Adminhtml\System\Config;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Modo\Gateway\Helper\Data as ModoHelper;
use \Modo\Gateway\Model\Adminhtml\System\Config\CallbackUrl;
use Modo\Gateway\Model\CredentialsValidator;
use Modo\Gateway\Service\ApiService as ModoService;

class CallbackUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ModoHelper
     */
    private $modoHelperMock;
    /**
     * @var CallbackUrl
     */
    private $callbackUrl;

    protected function setUp(): void
    {
        $this->modoHelperMock = $this->getMockBuilder(ModoHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->modoServiceMock = $this->getMockBuilder(ModoService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->credentialsValidatorMock = $this->getMockBuilder(CredentialsValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->callbackUrl = new CallbackUrl(
            $this->modoHelperMock,
            $this->modoServiceMock,
            $this->credentialsValidatorMock,
            $this->createMock(\Magento\Framework\Model\Context::class),
            $this->createMock(\Magento\Framework\Registry::class),
            $this->createMock(ScopeConfigInterface::class),
            $this->createMock(\Magento\Framework\App\Cache\TypeListInterface::class),
            null,
            null,
            []
        );
    }

    public function testAfterLoad(){
        $urlCallbackTest = 'url-callback-test';
        $this->modoHelperMock->expects($this->once())->method('getCallbackUrl')->willReturn($urlCallbackTest);
        $this->callbackUrl->afterLoad();
        $this->assertEquals($urlCallbackTest, $this->callbackUrl->getValue());
    }
}
