<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Rollpix\Payment\Gateway\Config\Config;
use Rollpix\Payment\Gateway\Config\PayNowConfig;
use Rollpix\Payment\Model\Method\PayNow;

/**
 * Retrieve config needed for checkout.
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var PayNowConfig
     */
    private $payNowconfig;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Constructor.
     *
     * @param Config $config
     * @param PayNowConfig $payNowConfig
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Config $config,
        PayNowConfig $payNowConfig,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->payNowConfig = $payNowConfig;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return [
            'payment' => [
                Config::CODE => [
                    'uiParams' => $this->config->getUiParams(),
                    'inContext' => $this->config->inContext(),
                    'summaryWidget' => $this->config->summaryWidgetEnabled(),
                    'checkoutUrl' => $this->urlBuilder->getUrl('rollpix/payment/checkout'),
                    'icon' => 'https://cdn.rollpix.io/e/images/rollpix-' . $this->config->getTheme() . '.png'
                ],
                PayNowConfig::CODE => [
                    'summaryWidget' => $this->payNowConfig->summaryWidgetEnabled(),
                    'numInstalments' => PayNow::NUM_INSTALMENTS,
                    'icon' => 'https://cdn.rollpix.io/e/images/rollpix-pay-now.png'
                ]
            ]
        ];
    }
}
