<?php

namespace Rollpix\Elebar\Controller\Page;


use Magento\Framework\App\Action\Context;
use Rollpix\Elebar\Model\Service\OrderService;

class Pay extends \Magento\Framework\App\Action\Action
{
    /**
     * @var OrderService
     */
    protected $orderService;

    public function __construct(
        Context $context,
        OrderService $orderService
    )
    {
        parent::__construct($context);
        $this->orderService = $orderService;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('orderid');
        $orderKeyUnverified = $this->getRequest()->getParam('orderkey');
        if($orderId != '' && $this->orderService->isOrderKeyValid($orderId,$orderKeyUnverified)) {
            $this->_view->loadLayout(['', 'elebar_gateway_order_pay']);
            $this->_view->renderLayout();
        }
        else{
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
    }
}