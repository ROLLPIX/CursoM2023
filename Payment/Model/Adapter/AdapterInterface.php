<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Model\Adapter;

/**
 * Interface AdapterInterface
 *
 * Sends HTTP requests via a proper adapter.
 */
interface AdapterInterface
{
    /**
     * Create a checkout using given parameters.
     *
     * @param array $params
     *
     * @return array
     *
     * @throws \Rollpix\Exceptions\ApiException
     */
    public function checkout(array $params);

    /**
     * Capture an order.
     *
     * @param string $id
     *
     * @return array
     *
     * @throws \Rollpix\Exceptions\ApiException
     */
    public function capture($id);

    /**
     * Refund a capture transaction.
     *
     * @param string $id
     * @param string $refundId
     * @param float $amount
     *
     * @return array
     *
     * @throws \Rollpix\Exceptions\ApiException
     */
    public function refund($id, $refundId, $amount);
}
