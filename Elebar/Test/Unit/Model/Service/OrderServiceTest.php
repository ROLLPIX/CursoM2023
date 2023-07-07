<?php
namespace Rollpix\Elebar\Test\Unit\Model\Service;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use \Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use Rollpix\Elebar\Model\Ui\ConfigProvider;
use Rollpix\Elebar\Observer\DataAssignObserver;
use Rollpix\Elebar\Service\ApiService;
use Rollpix\Elebar\Service\ApiService as RollpixService;

class OrderServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactoryMock;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryMock;

    /**
     * @var InvoiceOrderInterface
     */
    private $invoiceOrderMock;

    /**
     * @var RollpixService
     */
    private $elebarServiceMock;

    /**
     * @var RollpixHelper
     */
    private $elebarHelperMock;

    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteIdMock;
    /**
     * @var \Rollpix\Elebar\Model\Service\OrderService
     */
    private $orderService;
    /**
     * @var OrderCollection|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderCollectionMock;
    /**
     * @var Order|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderMock;
    /**
     * @var DataObject|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paymentMock;
    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit\Framework\MockObject\MockObject
     */
    private $selectMock;

    protected function setUp(): void
    {
        $this->orderCollectionFactoryMock = $this->getMockBuilder(OrderCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->orderCollectionMock = $this->getMockBuilder(OrderCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->selectMock = $this->getMockBuilder(\Magento\Framework\DB\Select::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->paymentMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Payment\Info::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->orderRepositoryMock = $this->getMockBuilder(OrderRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->invoiceOrderMock = $this->getMockBuilder(InvoiceOrderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->elebarServiceMock = $this->getMockBuilder(ApiService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->elebarHelperMock = $this->getMockBuilder(RollpixHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->maskedQuoteIdToQuoteIdMock = $this->getMockBuilder(MaskedQuoteIdToQuoteIdInterface::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->orderCollectionMock->expects($this->any())
            ->method('addFieldToFilter')
            ->willReturnSelf();
        $this->selectMock->method('join')->willReturnSelf();
        $this->selectMock->method('where')->willReturnSelf();
        $this->orderCollectionMock->expects($this->any())
            ->method('getSelect')
            ->willReturn($this->selectMock);
        $this->orderMock->method('getId')->willReturn(1);
        $this->orderMock->method('getPayment')->willReturn($this->paymentMock);
        $this->orderCollectionMock->expects($this->any())
            ->method('getFirstItem')
            ->willReturn($this->orderMock);
        $this->orderCollectionFactoryMock->method('create')->willReturn($this->orderCollectionMock);
        $this->orderRepositoryMock->method('save')->willReturn($this->orderMock);
        $this->orderRepositoryMock->method('get')->willReturn($this->orderMock);

        $this->orderService = new \Rollpix\Elebar\Model\Service\OrderService(
            $this->orderCollectionFactoryMock,
            $this->orderRepositoryMock,
            $this->invoiceOrderMock,
            $this->elebarServiceMock,
            $this->elebarHelperMock,
            $this->maskedQuoteIdToQuoteIdMock
        );
    }

    public function testPurchaseActionWithNumericQuote(){
        $quoteId = 1;
        $this->elebarHelperMock->method('getNewOrderStatus')->willReturn('pending');
        $this->assertEquals(['success' => true, 'order_id' => 1, 'message' => ''], $this->orderService->purchaseAction($quoteId));
    }

    public function testPurchaseActionWithMaskedQuote(){
        $quoteId = 'quote-mask';
        $this->maskedQuoteIdToQuoteIdMock->method('execute')->willReturn(1);
        $this->elebarHelperMock->method('getNewOrderStatus')->willReturn('pending');
        $this->assertEquals(['success' => true, 'order_id' => 1, 'message' => ''], $this->orderService->purchaseAction($quoteId));
    }

    public function testGetOrderIdByQuoteIdWithNumericQuote(){
        $this->assertEquals(['success' => true, 'order_id' => 1, 'message' => ''], $this->orderService->getOrderIdByQuoteId(1));
    }

    public function testGetOrderIdByQuoteIdWithMaskedQuote(){
        $this->maskedQuoteIdToQuoteIdMock->method('execute')->willReturn(1);
        $this->assertEquals(['success' => true, 'order_id' => 1, 'message' => ''], $this->orderService->getOrderIdByQuoteId('quote-mask'));
    }

    public function testGetQuoteIdByOrderId(){
        $this->orderMock->method('getQuoteId')->willReturn(1);
        $this->assertEquals(['success' => true, 'quote_id' => 1, 'message' => ''], $this->orderService->getQuoteIdByOrderId(1));
    }

    public function testGetOrderByIncrementId(){
        $this->assertEquals(['success' => true, 'order' => $this->orderMock, 'message' => ''], $this->orderService->getOrderByIncrementId(1));
    }

    public function testIsOrderKeyValid(){
        $this->paymentMock->method('getAdditionalInformation')->willReturn('b3JkZXIta2V5OjE=');
        $this->orderMock->method('getQuoteId')->willReturn(1);
        $this->assertTrue($this->orderService->isOrderKeyValid(1,'order-key'));
    }

    public function testIsOrderKeyValidWithException(){
        $this->paymentMock->method('getAdditionalInformation')->willThrowException(new \Exception(__('Exception')));
        $this->assertFalse($this->orderService->isOrderKeyValid(1,'order-key'));
    }

    public function testSaveOrder(){
        $this->assertEquals($this->orderMock,$this->orderService->saveOrder($this->orderMock));
    }

    public function testGeneratePaymentIntention(){
        $this->elebarServiceMock->method('createPaymentIntention')->willReturn(['response-test']);
        $this->assertEquals(['success' => true, 'data' => ['response-test'], 'message' => ''], $this->orderService->generatePaymentIntention(1));
    }

    public function testGeneratePaymentIntentionWithNoArray(){
        $this->elebarServiceMock->method('createPaymentIntention')->willReturn('response-test');
        $this->assertEquals(['success' => false, 'data' => '', 'message' => 'response-test'], $this->orderService->generatePaymentIntention(1));
    }

    public function testGeneratePaymentIntentionWithException(){
        $this->elebarServiceMock->method('createPaymentIntention')->willThrowException(new NoSuchEntityException(__('Exception')));
        $this->assertEquals(['success' => false, 'data' => '', 'message' => 'Exception'], $this->orderService->generatePaymentIntention(1));
    }

    public function testApproveOrder(){
        $this->invoiceOrderMock->method('execute')->willReturn(1);
        $this->elebarHelperMock->method('getApprovedOrderStatus')->willReturn('processing');
        $this->orderMock->method('getQuoteId')->willReturn(1);

        $this->assertInstanceOf(OrderInterface::class, $this->orderService->approveOrder(1));
    }

    public function testCancelOrderWithCancelFlagAndCanCancel(){
        $this->elebarHelperMock->method('canCancelOnFailure')->willReturn(true);
        $this->orderMock->method('canCancel')->willReturn(true);
        $this->orderMock->method('cancel')->willReturnSelf();
        $this->orderMock->method('getQuoteId')->willReturn(1);

        $this->assertEquals(['success' => true, 'message' => __("Order successfully canceled"),'quote_id' => 1],$this->orderService->cancelOrder(1));
    }

    public function testCancelOrderWithCancelFlagAndCanNotCancelBecauseAlreadyCancel(){
        $this->elebarHelperMock->method('canCancelOnFailure')->willReturn(true);
        $this->orderMock->method('canCancel')->willReturn(false);
        $this->orderMock->method('isCanceled')->willReturn(true);
        $this->orderMock->method('getQuoteId')->willReturn(1);

        $this->assertEquals(['success' => true, 'message' => __("Order already canceled"),'quote_id' => 1],$this->orderService->cancelOrder(1));
    }

    public function testCancelOrderWithCancelFlagAndCanNotCancelAndNotCanceled(){
        $this->elebarHelperMock->method('canCancelOnFailure')->willReturn(true);
        $this->orderMock->method('canCancel')->willReturn(false);
        $this->orderMock->method('isCanceled')->willReturn(false);
        $this->orderMock->method('getQuoteId')->willReturn(1);
        $this->assertEquals(['success' => false, 'message' => __("Can not cancel this orden"),'quote_id' => 1],$this->orderService->cancelOrder(1));
    }

    public function testCancelOrderWithoutCancelFlag(){
        $this->elebarHelperMock->method('canCancelOnFailure')->willReturn(false);
        $this->orderMock->method('getQuoteId')->willReturn(1);
        $this->assertEquals(['success' => false, 'message' => __("Order not canceled due to module configurations."),'quote_id' => 1],$this->orderService->cancelOrder(1));
    }

    public function testCancelOrderWithException(){
        $this->orderRepositoryMock->method('get')->willThrowException(new NoSuchEntityException(__('Exception')));
        $this->assertEquals(['success' => false, 'message' => __("Exception")],$this->orderService->cancelOrder(1));
    }

    public function testGetOrderByAttributeWithId(){
        $getOrderByAttributeTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Model\Service\OrderService::class,
            'getOrderByAttribute'
        );
        $getOrderByAttributeTestMethod->setAccessible(true);
        $result = $getOrderByAttributeTestMethod->invokeArgs($this->orderService,['attribute_code' => Order::QUOTE_ID,'value' => 1, 'retrieveId' => true]);
        $this->assertEquals(['success' => true, 'message' => '', 'order_id' => 1],$result);
    }

    public function testGetOrderByAttributeWithObject(){
        $getOrderByAttributeTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Model\Service\OrderService::class,
            'getOrderByAttribute'
        );
        $getOrderByAttributeTestMethod->setAccessible(true);
        $result = $getOrderByAttributeTestMethod->invokeArgs($this->orderService,['attribute_code' => Order::QUOTE_ID,'value' => 1, 'retrieveId' => false]);
        $this->assertEquals(['success' => true, 'message' => '', 'order' => $this->orderMock],$result);
    }

    public function testGetOrderByAttributeWithException(){
        $getOrderByAttributeTestMethod = new \ReflectionMethod(
            \Rollpix\Elebar\Model\Service\OrderService::class,
            'getOrderByAttribute'
        );
        $getOrderByAttributeTestMethod->setAccessible(true);
        $fakeOrderMock = new DataObject();
        $fakeOrderMock->setId(false);
        $this->orderCollectionMock->expects($this->any())
            ->method('getFirstItem')
            ->willThrowException(new \Exception(__('Exception')));
        $result = $getOrderByAttributeTestMethod->invokeArgs($this->orderService,['attribute_code' => Order::QUOTE_ID,'value' => 1, 'retrieveId' => false]);
        $this->assertEquals(['success' => false, 'message' => __('Exception')],$result);
    }

    public function testGetOrderToCancelCollection(){
        $this->assertInstanceOf(\Magento\Sales\Model\ResourceModel\Order\Collection::class,$this->orderService->getOrderToCancelCollection(1));
    }
}
