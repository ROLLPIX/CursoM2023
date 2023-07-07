<?php

namespace Rollpix\Elebar\Test\Unit\Block\Order\Payment;

use Magento\Framework\View\Asset\Repository;
use Rollpix\Elebar\Helper\Data as RollpixHelper;

class InfoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestMock;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactoryMock;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSessionMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timeZoneMock;

    /**
     * @var \Rollpix\Elebar\Helper\Data
     */
    protected $elebarHelperMock;

    /**
     * @var Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;
    /**
     * @var \Magento\Checkout\Model\Session|\PHPUnit\Framework\MockObject\MockObject
     */
    private $sessionMock;
    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;
    /**
     * @var \Rollpix\Elebar\Block\Order\Payment\Info
     */
    private $paymentInfo;
    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderMock;


    protected function setUp(): void
    {
        $this->contextMock = $this->getMockBuilder(\Magento\Framework\View\Element\Template\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock->method('getParam')->willReturn('1');

        $this->contextMock->method('getRequest')->willReturn($this->requestMock);

        $this->orderFactoryMock = $this->getMockBuilder(\Magento\Sales\Model\OrderFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->orderMock = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->orderMock->expects($this->any())
            ->method('load')
            ->willReturnSelf();
        $this->orderFactoryMock->method('create')->willReturn($this->orderMock);


        $this->sessionMock = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sessionMock = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->timeZoneMock = $this->getMockBuilder(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        //$this->repositoryMock = $this->createMock(Repository::class);
        $this->repositoryMock = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        //$this->repositoryMock->method('getUrl')->willReturn('elebar-logo-white');
        $this->repositoryMock->expects($this->any())
            ->method('getUrl')
            ->willReturn('elebar-logo-white');

        $this->contextMock->method('getAssetRepository')->willReturn($this->repositoryMock);

        $this->elebarHelperMock = $this->getMockBuilder(RollpixHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->elebarHelperMock->method('isDebugEnabled')->willReturn(false);

        $this->paymentInfo = new \Rollpix\Elebar\Block\Order\Payment\Info(
            $this->contextMock,
            //$this->requestMock,
            $this->orderFactoryMock,
            $this->sessionMock,
            $this->scopeConfigMock,
            $this->timeZoneMock,
            $this->elebarHelperMock,
            []
        );
    }

    public function testGetOrderIdFromRequest(){
        $this->assertEquals('1',$this->paymentInfo->getOrderIdFromRequest());
    }

    public function testGetOrder(){
        $this->assertInstanceOf(\Magento\Sales\Model\Order::class, $this->paymentInfo->getOrder());
    }

    public function testGetOrderId(){
        $this->orderMock->method('getIncrementId')->willReturn('1');
        $this->assertEquals('1',$this->paymentInfo->getOrderId());
    }

    public function testGetOrderCreatedAt(){
        $this->orderMock->method('getCreatedAt')->willReturn('2021-01-01');
        $this->timeZoneMock->method('date')->willReturn(new \DateTime('2021-01-01'));
        $this->assertEquals('01-01-2021',$this->paymentInfo->getOrderCreatedAt());
    }

    public function testGetTotal(){
        $this->orderMock->method('getBaseGrandTotal')->willReturn(500);
        $this->assertEquals('500.00',$this->paymentInfo->getTotal());
    }

    public function testGetPaymentMethod(){
        $this->elebarHelperMock->method('getTitle')->willReturn('Elebar');
        $this->elebarHelperMock->method('getDescription')->willReturn('Paga en un click con la app de Elebar');
        $this->assertEquals('Elebar - Paga en un click con la app de Elebar', $this->paymentInfo->getPaymentMethod());
    }

    public function testRollpixLogoUrl(){
        $this->assertEquals('elebar-logo-white',$this->paymentInfo->getRollpixLogoUrl());
    }
}