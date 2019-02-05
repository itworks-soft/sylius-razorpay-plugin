<?php

namespace ITWorks\SyliusRazorpayPlugin\Bridge;

use Razorpay\Api\Api;

/**
 * @author Hlib Synkovskyi <gleb.sinkovskiy@gmail.com>
 */
final class RazorpayBridge implements RazorpayBridgeInterface
{

	public static $checkoutUrl = 'https://checkout.razorpay.com/v1/checkout.js';

	/**
	 * @var Api
	 */
	private $api;

	/**
	 * @var string
	 */
	private $apiKey;

	/**
	 * @var string
	 */
	private $apiSecret;

    /**
     * {@inheritDoc}
     */
    public function setAuthorizationDataApi($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * {@inheritDoc}
     */
    public function capture($order)
    {
    	$api = $this->getApi();

        return $api->payment->fetch($order['razorpayPaymentId'])->capture(['amount'=> $order['amount']]);
    }

    private function getApi()
	{
		if ($this->api) {
			return $this->api;
		}

		$this->api = new Api($this->apiKey, $this->apiSecret);

		return $this->api;
	}

}
