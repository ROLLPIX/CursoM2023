<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Block\Widget;

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Rollpix\Payment\Gateway\Config\Config;
use Rollpix\Payment\Model\Adapter\ApiAdapter;

/**
 * Cart widget block.
 */
class Cart extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerCart
     */
    protected $cart;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param Template\Context $context
     * @param Config $paypalConfig
     * @param CustomerCart $cart
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        CustomerCart $cart,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
        $this->cart = $cart;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Get currency code.
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * Get cart subtotal.
     *
     * @return int
     */
    public function getSubtotal()
    {
        $totals = $this->cart->getQuote()->getTotals();
        return $totals['subtotal']['value'];
    }

    /**
     * @inheritdoc
     */
    public function _toHtml()
    {
        if ($this->config->isActive() && $this->config->isAvailable()) {
            return parent::_toHtml();
        }
        return '';
    }
}
