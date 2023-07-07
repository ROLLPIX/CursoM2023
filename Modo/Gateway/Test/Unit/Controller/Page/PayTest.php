<?php

namespace Modo\Gateway\Controller\Page;


use Magento\Framework\App\Action\Context;
use Modo\Gateway\Model\Service\OrderService;

class PayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OrderService
     */
    protected $orderServiceMock;
    /**
     * @var Pay
     */
    private $payController;

    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;
    /**
     * @var \Magento\Framework\App\Console\Request|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;
    /**
     * @var \Magento\Framework\App\ViewInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $viewMock;
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $resultRedirectFactoryMock;
    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit\Framework\MockObject\MockObject
     */
    private $resultRedirectMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->orderServiceMock = $this->createMock(OrderService::class);
        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewMock = $this->getMockBuilder(\Magento\Framework\App\ViewInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->resultRedirectMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->resultRedirectMock->expects($this->any())
            ->method('setPath')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->method('create')->willReturn($this->resultRedirectMock);
        $this->requestMock->method('getParam')->willReturn('param-test');
        $this->contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->viewMock->method('loadLayout')->willReturn(true);
        $this->viewMock->method('renderLayout')->willReturn(true);
        $this->contextMock->method('getView')->willReturn($this->viewMock);
        $this->contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);

        $this->payController = new \Modo\Gateway\Controller\Page\Pay(
            $this->contextMock,
            $this->orderServiceMock
        );
    }

    public function testExecuteOnSuccess(){
        $this->orderServiceMock->method('isOrderKeyValid')->willReturn(true);
        $this->assertNull($this->payController->execute());
    }

    public function testExecuteOnError(){
        $this->orderServiceMock->method('isOrderKeyValid')->willReturn(false);
        $this->assertInstanceOf(\Magento\Framework\Controller\Result\Redirect::class,$this->payController->execute());
    }
}