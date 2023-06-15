<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Block\Widget;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Rollpix\Payment\Gateway\Config\Config;
use Rollpix\Payment\Model\Adapter\ApiAdapter;

/**
 * Product widget block.
 */
class Product extends AbstractProduct
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Get Ui parameters.
     *
     * @return array
     */
    public function getUiParams()
    {
        return $this->config->getUiParams();
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
     * Get product final price.
     *
     * @return int
     */
    public function getFinalPrice()
    {
        return "";
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
