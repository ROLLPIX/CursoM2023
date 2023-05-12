<?php

namespace Rollpix\Shipping\Model\Carrier;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Psr\Log\LoggerInterface;
use Rollpix\Shipping\Model\ResourceModel\ShippingMethod\Collection as MethodCollection;

/**
 * @category   Rollpix
 * @package    Rollpix_Shipping
 * @author     rollpix@gmail.com
 * @website    https://www.rollpix.com
 */
class Shipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = 'rollpixshipping';

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    private MethodCollection       $methodCollection ;
    private Session                $session ;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        MethodCollection $methodCollection,
        Session          $session,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->methodCollection = $methodCollection ;
        $this->session = $session ;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Generates list of allowed carrier`s shipping methods
     * Displays on cart price rules page
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        return [$this->getCarrierCode() => __($this->getConfigData('name'))];
    }

    /**
     * Collect and get rates for storefront
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param RateRequest $request
     * @return DataObject|bool|null
     * @api
     */
    public function collectRates( RateRequest $request )
    {
        /**
         * Make sure that shipping method is enabled
         */
        if (! $this->isActive()) {
            return false;
        }
        $factor = $this->reverseCoupon() ;

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $methods = $this->methodCollection->getItems() ;
        foreach ( $methods as $shippingMethod ) {
            $newMethod = $this->_rateMethodFactory->create() ;
            $carrier   = $shippingMethod->getCode() ;
            $newMethod->setCarrier( $this->getCarrierCode() ) ;
            // $newMethod->setCarrierTitle( $shippingMethod->getTitle() ) ;
            $newMethod->setCarrierTitle( "$carrier" ) ;

            $newMethod->setMethod($this->getCarrierCode());
            $newMethod->setMethodTitle( $this->getConfigData( 'name' ) );

            $newMethod->setPrice( $shippingMethod->getCost() * $factor ) ;
            $newMethod->setCost( $shippingMethod->getCost() * $factor ) ;

            $result->append( $newMethod ) ;
        }

        return $result;
    }

    private function reverseCoupon() {
        $factor = 1 ;
        $quote = $this->session->getQuote() ;
        $couponCode = $quote->getData( 'coupon_code' ) ;
        $reverse = $this->getConfigData( 'reverse' ) ;

        if ( ( $reverse == 1 || $reverse == 'true' ) && $couponCode ) $factor = 1.5 ;
        return $factor ;
    }
}
