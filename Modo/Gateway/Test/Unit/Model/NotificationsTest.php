<?php
namespace Modo\Gateway\Model;


use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Modo\Gateway\Api\NotificationsInterface;
use Modo\Gateway\Model\Service\OrderService;
use Modo\Gateway\Helper\Data as ModoHelper;
use Modo\Gateway\Service\ApiService as ModoService;
use Modo\Gateway\Model\Notifications;

class NotificationsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Modo\Gateway\Helper\Data
     */
    private $modoHelperMock;

    /**
     * @var OrderService
     */
    private $orderServiceMock;
    /**
     * @var ModoService
     */
    private $modoService;
    /**
     * @var Notifications
     */
    private $notifications;


    protected function setUp(): void
    {
        $this->orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->modoHelperMock = $this->getMockBuilder(ModoHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->modoHelperMock->method('isDebugEnabled')->willReturn(false);

        $this->modoService = $this->getMockBuilder(ModoService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->notifications = new Notifications($this->orderServiceMock,$this->modoHelperMock,$this->modoService);
    }

    public function testNotifyOnSuccess(){
        $idTest = 1;
        $statusTest = 'test';
        $externalIntentionId = 'test-1';

        $orderEntity = $this->createMock(\Magento\Sales\Model\Order::class);
        $paymentMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Payment::class)
            ->disableOriginalConstructor()
            ->setMethods(['setAdditionalInformation'])
            ->getMock();
        $orderEntity->expects($this->once())->method('getPayment')->willReturn($paymentMock);
        $orderEntity->expects($this->once())->method('addCommentToStatusHistory')->willReturnSelf();


        $this->orderServiceMock->method('getOrderByIncrementId')->willReturn(['success' => true, 'order' => $orderEntity, 'message' => '']);
        $this->orderServiceMock->method('saveOrder')->willReturn($orderEntity);

        $notifyResultJson = $this->notifications->notify($idTest, $statusTest, $externalIntentionId);
        $notifyResultArray = json_decode($notifyResultJson,true);
        $this->assertTrue(is_array($notifyResultArray));
        $this->arrayHasKey('status',$notifyResultArray);
        $this->arrayHasKey('message',$notifyResultArray);
        $this->assertTrue($notifyResultArray['status']);
        $this->assertTrue($notifyResultArray['message'] == 'OK');
    }

    public function testNotifyOnSuccessWithStatusScanned(){
        $idTest = 1;
        $statusTest = 'SCANNED';
        $externalIntentionId = 'test-1';

        $orderEntity = $this->createMock(\Magento\Sales\Model\Order::class);
        $paymentMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Payment::class)
            ->disableOriginalConstructor()
            ->setMethods(['setAdditionalInformation'])
            ->getMock();
        $orderEntity->expects($this->once())->method('getPayment')->willReturn($paymentMock);
        $orderEntity->expects($this->once())->method('addCommentToStatusHistory')->willReturnSelf();


        $this->orderServiceMock->method('getOrderByIncrementId')->willReturn(['success' => true, 'order' => $orderEntity, 'message' => '']);
        $this->orderServiceMock->method('saveOrder')->willReturn($orderEntity);

        $notifyResultJson = $this->notifications->notify($idTest, $statusTest, $externalIntentionId);
        $notifyResultArray = json_decode($notifyResultJson,true);
        $this->assertTrue(is_array($notifyResultArray));
        $this->arrayHasKey('status',$notifyResultArray);
        $this->arrayHasKey('message',$notifyResultArray);
        $this->assertTrue($notifyResultArray['status']);
        $this->assertTrue($notifyResultArray['message'] == 'OK');
    }

    public function testNotifyOnSuccessWithStatusAccepted(){
        $idTest = 1;
        $statusTest = 'ACCEPTED';
        $externalIntentionId = 'test-1';

        $orderEntity = $this->createMock(\Magento\Sales\Model\Order::class);
        $paymentMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Payment::class)
            ->disableOriginalConstructor()
            ->setMethods(['setAdditionalInformation'])
            ->getMock();
        $orderEntity->expects($this->once())->method('getPayment')->willReturn($paymentMock);
        $orderEntity->expects($this->once())->method('addCommentToStatusHistory')->willReturnSelf();

        $this->orderServiceMock->method('approveOrder')->willReturn($orderEntity);

        $this->orderServiceMock->method('getOrderByIncrementId')->willReturn(['success' => true, 'order' => $orderEntity, 'message' => '']);
        $this->orderServiceMock->method('saveOrder')->willReturn($orderEntity);

        $notifyResultJson = $this->notifications->notify($idTest, $statusTest, $externalIntentionId);
        $notifyResultArray = json_decode($notifyResultJson,true);
        $this->assertTrue(is_array($notifyResultArray));
        $this->arrayHasKey('status',$notifyResultArray);
        $this->arrayHasKey('message',$notifyResultArray);
        $this->assertTrue($notifyResultArray['status']);
        $this->assertTrue($notifyResultArray['message'] == 'OK');
    }

    public function testNotifyOnFailure(){
        $idTest = 1;
        $statusTest = 'test';
        $externalIntentionId = 'test-1';

        $this->orderServiceMock->method('getOrderByIncrementId')->willReturn(['success' => false, 'message' => 'Order not found']);

        $notifyResultJson = $this->notifications->notify($idTest, $statusTest, $externalIntentionId);
        $notifyResultArray = json_decode($notifyResultJson,true);
        $this->assertTrue(is_array($notifyResultArray));
        $this->arrayHasKey('status',$notifyResultArray);
        $this->arrayHasKey('message',$notifyResultArray);
        $this->assertTrue(!$notifyResultArray['status']);
        $this->assertEquals('Order not found', $notifyResultArray['message']);
    }
}
