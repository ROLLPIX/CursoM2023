<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Model\Adapter;

use DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\Method\Logger;
use Rollpix\Exceptions\ApiException;
use Rollpix\Payment\Gateway\Config\Config;
use Psr\Log\LoggerInterface;

/**
 * Rollpix Api Adapter.
 */
class ApiAdapter
{
    /**
     * @var \Rollpix\Payment
     */
    protected $client;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Logger
     */
    protected $customLogger;

    /**
     * Constructor.
     *
     * @param Config $config
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        Logger $customLogger
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->customLogger = $customLogger;
    }

    /**
     * Send a request and return the response.
     *
     * @param string $method
     * @param string $path
     * @param array $params
     *
     * @return array
     *
     * @throws ApiException
     * @throws LocalizedException
     */
    public function request($method, $path, array $params = [])
    {
        if ($this->client === null) {
            throw new LocalizedException(__('Rollpix is not properly configured.'));
        }

        try {
            /** @var \Rollpix\Http\Response $response */
            $response = $this->client->request($method, $path, $params);
        } catch (ApiException $e) {
            $this->logger->critical($e->getMessage());
            $response = $e->getResponse();
            throw $e;
        } finally {
            $this->customLogger->debug([
                'path' => $path,
                'request' => $params,
                'response' => $response->json()
            ]);
        }
        return $response->json();
    }

    /**
     * Convert float to JSON serializable instance.
     *
     * @param float $value
     *
     * @return Decimal
     * phpcs:disable Magento2.Functions.StaticFunction
     */
 

    /**
     * Convert datetime to JSON serializable instance.
     *
     * @param string $value
     *
     * @return Date
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function datetime($value)
    {
        return Date::fromDateTime(new DateTime($value));
    }
}
