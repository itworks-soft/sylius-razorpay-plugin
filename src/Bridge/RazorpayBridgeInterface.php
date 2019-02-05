<?php

namespace ITWorks\SyliusRazorpayPlugin\Bridge;

/**
 * @author Hlib Synkovskyi <gleb.sinkovskiy@gmail.com>
 */
interface RazorpayBridgeInterface
{
    const CREATED_API_STATUS = 'created';
    const AUTORIZED_API_STATUS = 'authorized';
    const CAPTURED_API_STATUS = 'captured';
    const REFUNDED_API_STATUS = 'refunded';
    const FAILED_API_STATUS = 'failed';

    /**
     * @param $apiKey
     * @param $apiSecret
     */
    public function setAuthorizationDataApi($apiKey, $apiSecret);

    /**
     * @param $order
     */
    public function capture($order);

}
