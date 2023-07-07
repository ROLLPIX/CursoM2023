<?php


namespace Rollpix\Elebar\Test\Unit\Model\Adminhtml\System\Config;

use Magento\Framework\App\Cache\Type\Config as Cache;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Rollpix\Elebar\Service\ApiService;
use Rollpix\Elebar\Model\Adminhtml\System\Config\InvalidateCacheOnChange;
class InvalidateCacheOnChangeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Cache
     */
    private $cacheMock;

    /**
     * @var InvalidateCacheOnChange
     */
    private $invalidateCacheOnChange;

    protected function setUp(): void
    {
        $this->cacheMock = $this->createMock(Cache::class);
        $this->contextMock = $this->createMock(\Magento\Framework\Model\Context::class);
        $managerInterface = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        $managerInterface->method('dispatch')->willReturn($managerInterface);
        $this->contextMock->method('getEventDispatcher')->willReturn($managerInterface);

        $this->invalidateCacheOnChange = new InvalidateCacheOnChange(
            $this->cacheMock,
            $this->contextMock,
            $this->createMock(\Magento\Framework\Registry::class),
            $this->createMock(ScopeConfigInterface::class),
            $this->createMock(\Magento\Framework\App\Cache\TypeListInterface::class),
            null,
            null,
            []
        );

        $this->invalidateCacheOnChange->setDataChanges(true);
    }

    public function testBeforeSave()
    {
        $this->cacheMock->expects($this->exactly(2))->method('test')->willReturn(true);
        $this->cacheMock->expects($this->exactly(2))->method('remove')->willReturn(true);

        $result = $this->invalidateCacheOnChange->beforeSave();
        $this->assertInstanceOf(\Magento\Framework\Model\AbstractModel::class,$result);
    }


}