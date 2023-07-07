<?php

namespace Modo\Gateway\Api;

interface NotificationsInterface
{
    /**
     * @param string $id
     * @param int $amount
     * @param string $status
     * @param string $external_intention_id
     * @param string $gateway_site_transaction_id
     * @param string $gateway_transaction_id
     * @param mixed $signature
     * @return mixed
     */
    public function notify($id, $amount, $status, $external_intention_id, $gateway_site_transaction_id, $gateway_transaction_id, $signature);

}