<?php


namespace Modo\Gateway\Test\Unit\Model\Adminhtml\System\Config;

use Magento\Framework\App\Cache\Type\Config as Cache;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Modo\Gateway\Service\ApiService;
use Modo\Gateway\Model\Adminhtml\System\Config\InvalidateCacheOnChange;
class EncryptedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Cache
     */
    private $cacheMock;

    /**
     * @var \Modo\Gateway\Model\Adminhtml\System\Config\Encrypted
     */
    private $encrypted;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $encrypterMock;
    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    protected function setUp(): void
    {
        $this->cacheMock = $this->createMock(Cache::class);
        $this->encrypterMock = $this->createMock(\Magento\Framework\Encryption\EncryptorInterface::class);
        $this->contextMock = $this->createMock(\Magento\Framework\Model\Context::class);
        $managerInterface = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        $managerInterface->method('dispatch')->willReturn($managerInterface);
        $this->contextMock->method('getEventDispatcher')->willReturn($managerInterface);

        $this->encrypted = new \Modo\Gateway\Model\Adminhtml\System\Config\Encrypted(
            $this->cacheMock,
            $this->encrypterMock,
            $this->contextMock,
            $this->createMock(\Magento\Framework\Registry::class),
            $this->createMock(ScopeConfigInterface::class),
            $this->createMock(\Magento\Framework\App\Cache\TypeListInterface::class),
            null,
            null,
            []
        );

        $this->encrypted->setDataChanges(true);
    }

    public function testBeforeSave()
    {
        $this->cacheMock->expects($this->exactly(2))->method('test')->willReturn(true);
        $this->cacheMock->expects($this->exactly(2))->method('remove')->willReturn(true);

        $result = $this->encrypted->beforeSave();
        $this->assertInstanceOf(\Magento\Framework\Model\AbstractModel::class,$result);
    }


}