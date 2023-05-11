<?php

namespace Rollpix\Shipping\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Psr\Log\LoggerInterface;

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
    protected $_code = 'mpshipping';

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $rateMethodFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
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
        $methods = array(
            0 => array(
                'code' => 'code1',
                'title' => 'Moto',
                'method' => 'Rollpix Shipping',
                'cost' => 11.2
            ),
            1 => array(
                'code' => 'code2',
                'title' => 'Auto',
                'method' => 'Rollpix Shipping',
                'cost' => 32.48
            ),
            2 => array(
                'code' => 'code3',
                'title' => 'Veni a buscarlo',
                'method' => 'Rollpix Shipping',
                'cost' => 0
            ),

        );
        return $methods;
    }

    /**
     * Collect and get rates for storefront
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param RateRequest $request
     * @return DataObject|bool|null
     * @api
     */
    public function collectRates(RateRequest $request)
    {
        /**
         * Make sure that shipping method is enabled
         */
        if (! $this->isActive()) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        /** @var Method $method */
        $result = $this->rateResultFactory->create();
        $allowedMethods = $this->getAllowedMethods();
        foreach ($allowedMethods as $key) {

            $method = $this->rateMethodFactory->create();

            $method->setCarrier($this->_code);
            
            $method->setCarrierTitle($key["title"]);

            $method->setMethod($key["code"]);
            $method->setMethodTitle($key["method"]);

            $shippingCost = (float)$key["cost"];
            $method->setPrice($shippingCost);
            $method->setCost($shippingCost);


            $result->append($method);
        }
        return $result;
    }

}
