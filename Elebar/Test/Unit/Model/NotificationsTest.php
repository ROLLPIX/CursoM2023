<?php
namespace Rollpix\Elebar\Model;


use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Rollpix\Elebar\Api\NotificationsInterface;
use Rollpix\Elebar\Model\Service\OrderService;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use Rollpix\Elebar\Service\ApiService as RollpixService;
use Rollpix\Elebar\Model\Notifications;

class NotificationsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Rollpix\Elebar\Helper\Data
     */
    private $elebarHelperMock;

    /**
     * @var OrderService
     */
    private $orderServiceMock;
    /**
     * @var RollpixService
     */
    private $elebarService;
    /**
     * @var Notifications
     */
    private $notifications;


    protected function setUp(): void
    {
        $this->orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->elebarHelperMock = $this->getMockBuilder(RollpixHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->elebarHelperMock->method('isDebugEnabled')->willReturn(false);

        $this->elebarService = $this->getMockBuilder(RollpixService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->notifications = new Notifications($this->orderServiceMock,$this->elebarHelperMock,$this->elebarService);
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
