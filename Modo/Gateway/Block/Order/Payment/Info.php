<?php

namespace Modo\Gateway\Block\Order\Payment;

use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Modo\Gateway\Helper\Data;

class Info extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timeZone;

    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * @var \Modo\Gateway\Helper\Data
     */
    protected $modoHelper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $currentOrder;

    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        TimezoneInterface $timezone,
        Data $modoHelper,
        array $data = []
    )
    {
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_timeZone = $timezone;
        $this->_assetRepo = $context->getAssetRepository();
        $this->modoHelper = $modoHelper;
        $this->currentOrder = null;
        parent::__construct(
            $context,
            $data
        );
        $this->setTemplate('order/payment-info.phtml');
    }

    public function getOrderIdFromRequest()
    {
        return $this->_request->getParam('orderid');
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if($this->currentOrder == null) {
            $this->currentOrder = $this->_orderFactory->create()->load($this->getOrderIdFromRequest());
        }

        return $this->currentOrder;
    }

    public function getOrderId(){
        return $this->getOrder()->getIncrementId();
    }

    public function getOrderCreatedAt(){
        $createdAt = $this->getOrder()->getCreatedAt();
        return $this->_timeZone->date(new \DateTime($createdAt))->format('d-m-Y');

    }

    /**
     * @return float|string
     */
    public function getTotal()
    {
        $order = $this->getOrder();
        $total = $order->getBaseGrandTotal();

        return number_format($total, 2, '.', '');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPaymentMethod()
    {
        return $this->modoHelper->getTitle() . ' - ' . $this->modoHelper->getDescription();
    }

    public function getModoLogoUrl(){
        return $this->_assetRepo->getUrl("Modo_Gateway::images/modo-logo-white.png");
    }
}