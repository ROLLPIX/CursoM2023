<?php
namespace Rollpix\Elebar\Test\Unit\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Rollpix\Elebar\Model\Service\OrderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

class CancelOrdersTest extends \PHPUnit\Framework\TestCase
{
    const STORE_ID_OPTION = 'store_id';
    /**
     * @var State
     */
    protected $stateMock;

    /**
     * @var OrderService
     */
    protected $orderServiceMock;
    /**
     * @var TimezoneInterface
     */
    protected $timezoneMock;
    /**
     * @var \Rollpix\Elebar\Console\Command\CancelOrders
     */
    private $cancelOrdersCommand;
    /**
     * @var mixed|\PHPUnit\Framework\MockObject\MockObject|InputInterface
     */
    private $inputCommand;
    /**
     * @var mixed|\PHPUnit\Framework\MockObject\MockObject|OutputInterface
     */
    private $outputCommand;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ProgressBar
     */
    private $progressBarMock;

    /**
     * @var Order|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderMock;
    /**
     * @var mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderServiceFactoryMock;

    protected function setUp(): void
    {
        $this->stateMock = $this->createMock(State::class);
        $this->orderServiceFactoryMock = $this->createMock(\Rollpix\Elebar\Model\Service\OrderServiceFactory::class);
        $this->orderServiceMock = $this->createMock(OrderService::class);
        $this->timezoneMock = $this->createMock(TimezoneInterface::class);
        $this->inputCommand = $this->createMock(InputInterface::class);
        $this->outputCommand = $this->createMock(OutputInterface::class);

        $this->orderServiceFactoryMock->method('create')->willReturn($this->orderServiceMock);
        $this->timezoneMock->method('date')->willReturn(new \DateTime());
        $this->outputCommand->method('writeln')->willReturnSelf();
        $this->inputCommand->method('getOption')->willReturn(1);

        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->ordersCollectionMock = $this->createMock(OrderCollection::class);
        $this->ordersCollectionMock->method('getItems')->willReturn([$this->orderMock]);

        $this->orderServiceMock->method('getOrderToCancelCollection')->willReturn($this->ordersCollectionMock);
        $this->ordersCollectionMock->method('getData')->willReturn([$this->orderMock]);
        $this->cancelOrdersCommand = new \Rollpix\Elebar\Console\Command\CancelOrders($this->stateMock,$this->orderServiceFactoryMock, $this->timezoneMock,null);
    }

    public function testConfigure(){
        $configureTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Console\Command\CancelOrders::class,
            'configure'
        );
        $configureTestMethod->setAccessible(true);
        $result = $configureTestMethod->invoke($this->cancelOrdersCommand);
        $this->assertNull($result);
    }

    public function testCancelOrders(){
        $this->assertEquals(\Magento\Framework\Console\Cli::RETURN_SUCCESS, $this->cancelOrdersCommand->cancelOrders($this->inputCommand,$this->outputCommand));
    }
}
