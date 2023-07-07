<?php


namespace Rollpix\Elebar\Test\Unit\Model\Adminhtml\System\Config;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use \Rollpix\Elebar\Model\Adminhtml\System\Config\CallbackUrl;
use Rollpix\Elebar\Model\CredentialsValidator;
use Rollpix\Elebar\Service\ApiService as RollpixService;

class CallbackUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RollpixHelper
     */
    private $elebarHelperMock;
    /**
     * @var CallbackUrl
     */
    private $callbackUrl;

    protected function setUp(): void
    {
        $this->elebarHelperMock = $this->getMockBuilder(RollpixHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->elebarServiceMock = $this->getMockBuilder(RollpixService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->credentialsValidatorMock = $this->getMockBuilder(CredentialsValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->callbackUrl = new CallbackUrl(
            $this->elebarHelperMock,
            $this->elebarServiceMock,
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
        $this->elebarHelperMock->expects($this->once())->method('getCallbackUrl')->willReturn($urlCallbackTest);
        $this->callbackUrl->afterLoad();
        $this->assertEquals($urlCallbackTest, $this->callbackUrl->getValue());
    }
}
