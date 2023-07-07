<?php
namespace Rollpix\Elebar\Controller\Order;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Rollpix\Elebar\Model\Service\OrderService;

class OperationsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OrderService
     */
    protected $orderServiceMock;

    /**
     * @var Session
     */
    protected $checkoutSessionMock;

    /**
     * @var ResultFactory
     */
    protected $resultFactoryMock;
    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;
    /**
     * @var Operations
     */
    private $operationController;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->orderServiceMock = $this->createMock(OrderService::class);
        $this->checkoutSessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['setLastSuccessQuoteId','setLastQuoteId','setLastOrderId'])
            ->getMock();
        $this->resultFactoryMock = $this->createMock(ResultFactory::class);
        $this->resultRediretMock = $this->createMock(\Magento\Framework\Controller\Result\Redirect::class);

        $this->resultRedirectFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->resultMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->resultMock->expects($this->any())
            ->method('setData')
            ->willReturnSelf();

        $this->resultFactoryMock->method('create')->willReturn($this->resultMock);
        $this->contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);

        $this->resultRediretMock->method('setPath')->willReturnSelf();
        $this->resultRedirectFactory->method('create')->willReturn($this->resultRediretMock);
        $this->contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactory);


        $this->checkoutSessionMock->method('setLastSuccessQuoteId')->willReturnSelf();
        $this->checkoutSessionMock->method('setLastQuoteId')->willReturnSelf();
        $this->checkoutSessionMock->method('setLastOrderId')->willReturnSelf();

        $this->operationController = new \Rollpix\Elebar\Controller\Order\Operations(
            $this->contextMock,
            $this->orderServiceMock,
            $this->checkoutSessionMock,
            $this->resultFactoryMock
        );
    }

    public function testExecuteWithoutOperation(){
        $this->requestMock->method('getParam')->willReturn('no_operation');
        $this->assertEquals('', $this->operationController->execute());
    }

    public function testPurchase(){
        $this->requestMock->method('getParam')->willReturn('purchase');
        $this->orderServiceMock->method('purchaseAction')->willReturn(['success' => true, 'order_id' => 1]);
        $this->assertInstanceOf(\Magento\Framework\Controller\Result\Json::class, $this->operationController->execute());
    }
    public function testPurchaseOnError(){
        $this->requestMock->method('getParam')->willReturn('purchase');
        $this->orderServiceMock->method('purchaseAction')->willReturn(['success' => false, 'message' => 'error-mock', 'order_id' => 1]);
        $this->assertInstanceOf(\Magento\Framework\Controller\Result\Json::class, $this->operationController->execute());
    }

    public function testGeneratePaymentIntention(){
        $this->requestMock->method('getParam')->willReturn('generate_payment_intention');
        $this->orderServiceMock->method('generatePaymentIntention')->willReturn(['success' => false, 'data' => '', 'message' => '']);
        $this->assertInstanceOf(\Magento\Framework\Controller\Result\Json::class, $this->operationController->execute());
    }

    public function testRedirectToOnepageFailure(){
        /*$this->requestMock->method('getParam')->with('operation')->will($this->returnValue('redirect_to_onepage'));
        $this->requestMock->method('getParam')->with('orderid')->will($this->returnValue(1));
        $this->requestMock->method('getParam')->with('onepage')->will($this->returnValue('failure'));*/
        $this->requestMock->method('getParam')->willReturn('redirect_to_onepage');

        $this->orderServiceMock->method('getQuoteIdByOrderId')->willReturn(['success' => false, 'url' => '', 'message' => '']);
        $this->assertInstanceOf(\Magento\Framework\Controller\Result\Redirect::class, $this->operationController->execute());
    }

    public function testRedirectToOnepageSuccess(){
        /*$this->requestMock->method('getParam')->with('operation')->will($this->returnValue('redirect_to_onepage'));
        $this->requestMock->method('getParam')->with('orderid')->will($this->returnValue(1));
        $this->requestMock->method('getParam')->with('onepage')->will($this->returnValue('failure'));*/
        $this->requestMock->method('getParam')->willReturn('redirect_to_onepage');

        $this->orderServiceMock->method('getQuoteIdByOrderId')->willReturn(['success' => true, 'message' => '','quote_id' => 1]);
        $this->assertInstanceOf(\Magento\Framework\Controller\Result\Redirect::class, $this->operationController->execute());
    }


    public function testCancelOrder(){
        $this->requestMock->method('getParam')->willReturn('cancel_order');
        $this->orderServiceMock->method('cancelOrder')->willReturn(['success' => true, 'message' => '', 'quote_id' => 1]);
        $this->assertInstanceOf(\Magento\Framework\Controller\Result\Redirect::class, $this->operationController->execute());
    }
}
