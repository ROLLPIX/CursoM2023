<?php
namespace Rollpix\Elebar\Model\Service;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use Rollpix\Elebar\Model\Ui\ConfigProvider;
use Rollpix\Elebar\Observer\DataAssignObserver;
use Rollpix\Elebar\Service\ApiService as RollpixService;

class OrderService
{
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var InvoiceOrderInterface
     */
    private $invoiceOrder;

    /**
     * @var RollpixService
     */
    private $elebarService;

    /**
     * @var RollpixHelper
     */
    private $elebarHelper;

    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    public function __construct
    (
        OrderCollectionFactory $orderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        InvoiceOrderInterface $invoiceOrder,
        RollpixService $elebarService,
        RollpixHelper $elebarHelper,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->invoiceOrder = $invoiceOrder;
        $this->elebarService = $elebarService;
        $this->elebarHelper = $elebarHelper;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
    }

    /**
     * @param $quoteId
     * @return array
     */
    public function purchaseAction($quoteId){
        if(!is_numeric($quoteId)){
            $quoteId = $this->maskedQuoteIdToQuoteId->execute($quoteId);
        }
        $result = $this->getOrderByAttribute(OrderInterface::QUOTE_ID,$quoteId, false);
        if($result['success']) {
            $order = $result['order'];
            $order->setStatus($this->elebarHelper->getNewOrderStatus());
            $this->saveOrder($order);
            unset($result['order']);
            $result['order_id'] = $order->getId();
        }
        return $result;
    }

    /**
     * @param $quoteId
     * @return array
     */
    public function getOrderIdByQuoteId($quoteId){
        if(!is_numeric($quoteId)){
            $quoteId = $this->maskedQuoteIdToQuoteId->execute($quoteId);
        }
        return $this->getOrderByAttribute(OrderInterface::QUOTE_ID, $quoteId, true);
    }

    /**
     * @param $orderId
     * @return array
     */
    public function getQuoteIdByOrderId($orderId){
        $result = $this->getOrderByAttribute(OrderInterface::ENTITY_ID, $orderId, false);
        if($result['success']){
            $order = $result['order'];
            unset($result['order']);
            $result['quote_id'] = $order->getQuoteId();
        }
        return $result;
    }

    /**
     * @param $incrementId
     * @return array
     */
    public function getOrderByIncrementId($incrementId){
        return $this->getOrderByAttribute(OrderInterface::INCREMENT_ID, $incrementId, false);
    }

    public function isOrderKeyValid($orderId,$orderKeyUnverified){
        $orderKeyValid = false;
        try {
            $order = $this->orderRepository->get($orderId);
            $orderKey = (string)$order->getPayment()->getAdditionalInformation(\Rollpix\Elebar\Observer\DataAssignObserver::Elebar_ORDER_KEY);
            if(isset($orderKey)){
                $orderKeyDecoded = explode(':',base64_decode($orderKey));
                $orderKeyValid = $orderKeyDecoded[0] == $orderKeyUnverified && $orderKeyDecoded[1] == $order->getQuoteId();
            }
        }
        catch (\Exception $e){
        }
        return $orderKeyValid;
    }

    /**
     * @param $order
     * @return OrderInterface
     */
    public function saveOrder($order)
    {
        return $this->orderRepository->save($order);
    }

    /**
     * @param $attribute_code
     * @param $value
     * @param $retrieveId
     * @return array
     */
    private function getOrderByAttribute($attribute_code, $value, $retrieveId){
        $result = ['success' => false, 'message' => ''];
        try {
            /**
             * @var Order $orderLoaded
             */
            $orderLoaded = $this->orderCollectionFactory->create()->addFieldToFilter($attribute_code, ['eq' => $value])->getFirstItem();
            if($orderLoaded->getId()) {
                if($retrieveId) {
                    $result['order_id'] = $orderLoaded->getId();
                }
                else{
                    $result['order'] = $orderLoaded;
                }
                $result['success'] = true;
            }
            else{
                $result['message'] = __('Order with %1 %2 not found',$attribute_code,$value);
            }
        }catch (\Exception $exception){
            $result['message'] = $exception->getMessage();
        }
        return $result;
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function getPaymentIntention($pId){
        $result = ['success' => false, 'data' => '', 'message' => ''];
        try{
           $response = $this->elebarService->getPaymentIntention($pId);

                if (is_array($response)) {
                    $result['success'] = true;
                    $result['data'] = $response;
                } else {
                    $result['message'] = $response;
                }

        }catch (NoSuchEntityException $noSuchEntityException){
            $result['message'] = $noSuchEntityException->getMessage();
        }
        return $result;
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function generatePaymentIntention($orderId){
        $result = ['success' => false, 'data' => '', 'message' => ''];
        try{
            /**
             * @var OrderInterface $order
             */
            $order = $this->orderRepository->get($orderId);
            if(!$order->isCanceled()) {
                $this->elebarHelper->log("StoreID->".   $order->getStoreId());
                $response = $this->elebarService->createPaymentIntention( $order->getStoreId(),$order->getIncrementId(), $order->getTotalItemCount(), $order->getBaseGrandTotal());

                if (is_array($response)) {
                    $result['success'] = true;
                    $result['data'] = $response;
                } else {
                    $result['message'] = $response;
                }
            }

        }catch (NoSuchEntityException $noSuchEntityException){
            $result['message'] = $noSuchEntityException->getMessage();
        }
        return $result;
    }

    /**
     * @param string $orderId
     * @return OrderInterface
     */
    public function approveOrder($orderId){
        $this->invoiceOrder->execute($orderId,true,[],true);
        $order = $this->orderRepository->get($orderId);
        $order->setStatus($this->elebarHelper->getApprovedOrderStatus());
        return $this->saveOrder($order);
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function cancelOrder($orderId)
    {
        $result = ['success' => false, 'message' => ''];
        try{
            /**
             * @var OrderInterface $order
             */
            $order = $this->orderRepository->get($orderId);
            if ($this->elebarHelper->canCancelOnFailure()) {
                if ($order->canCancel()) {
                    $order->cancel();
                    $result['success'] = true;
                    $result['message'] = __("Order successfully canceled");
                } else {
                    if ($order->isCanceled()) {
                        $result['success'] = true;
                        $result['message'] = __("Order already canceled");
                    } else {
                        $result['message'] = __("Can not cancel this orden");
                    }
                }
            }
            else{
                $result['message'] = __('Order not canceled due to module configurations.');
            }
            $order->addCommentToStatusHistory(__('Rollpix info: ') . $result['message']);
            $this->saveOrder($order);
            $result['quote_id'] = $order->getQuoteId();
        }catch (NoSuchEntityException $noSuchEntityException){
            $result['message'] = $noSuchEntityException->getMessage();
        }
        return $result;
    }

    /**
     * @param $storeId
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrderToCancelCollection($storeId){
        $paymentMethod = ConfigProvider::CODE;
        $collection = $this->orderCollectionFactory->create();
        $collection
            ->addFieldToFilter('main_table.store_id', ['eq' => $storeId])
            ->addFieldToFilter('main_table.status', ['eq' => $this->elebarHelper->getNewOrderStatus()])
            ->getSelect()->join(
                ["sop" => "sales_order_payment"],
                'main_table.entity_id = sop.parent_id',
                array('method')
            )->where("sop.method = \"$paymentMethod\"");
        return $collection;
    }
}
