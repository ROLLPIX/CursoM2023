<?php
/**
 * Copyright © Rollpix. All rights reserved.
 * See LICENSE for license details.
 */
namespace Rollpix\Payment\Model\Adapter;

/**
 * Rollpix RESTful Adapter.
 */
class RESTfulAdapter extends ApiAdapter implements AdapterInterface
{
    /**
     * Send a POST request and return the response.
     *
     * @param string $path
     * @param array $params
     *
     * @return array
     *
     * @throws \Rollpix\Exceptions\ApiException
     */
    public function post($path, array $params = [])
    {
        return $this->request('POST', $path, $params);
    }

    /**
     * @inheritdoc
     */
    public function checkout(array $params)
    {
        return $this->post('/checkouts', $params);
    }

    /**
     * @inheritdoc
     */
    public function capture($id)
    {
        return $this->post('/orders/' . $id . '/capture');
    }

    /**
     * @inheritdoc
     */
    public function refund($id, $refundId, $amount)
    {
        return $this->post('/orders/' . $id . '/refunds', [
            'refund_id' => $refundId,
            'amount' => self::decimal($amount)
        ]);
    }
}
