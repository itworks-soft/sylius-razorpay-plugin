<?php

namespace ITWorks\SyliusRazorpayPlugin\Action;

use ITWorks\SyliusRazorpayPlugin\Bridge\RazorpayBridgeInterface;
use ITWorks\SyliusRazorpayPlugin\SetRazorpay;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;
use Payum\Core\Payum;

/**
 * @author Hlib Synkovskyi <gleb.sinkovskiy@gmail.com>
 */
final class RazorpayAction implements ApiAwareInterface, ActionInterface
{
    private $api = [];

    /**
     * @var RazorpayBridgeInterface
     */
    private $razorpayBridge;

    /**
     * @var Payum
     */
    private $payum;

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * @param RazorpayBridgeInterface $razorpayBridge
     * @param Payum                   $payum
     */
    public function __construct(RazorpayBridgeInterface $razorpayBridge, Payum $payum)
    {
        $this->payum = $payum;
        $this->razorpayBridge = $razorpayBridge;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $apiKey = $this->api['api_key'];
        $apiSecret = $this->api['api_secret'];

        $razorpay = $this->getRazorpayBridge();
        $razorpay->setAuthorizationDataApi($apiKey, $apiSecret);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (isset($_POST['razorpay_payment_id'])) { // Autorization response
        	$model['razorpayPaymentId'] = $_POST['razorpay_payment_id'];

        	if ($model['razorpayPaymentId'] == 'null') {
				$model['razorpayStatus'] = RazorpayBridgeInterface::FAILED_API_STATUS;
				return;
			}

            $payment = $razorpay->capture($model);

            if (!$payment) {
            	$model['razorpayStatus'] = RazorpayBridgeInterface::FAILED_API_STATUS;
            	return;
			}

			$model['razorpayStatus'] = $payment->status;
			$request->setModel($model);

			return;
        }

        /**
         * @var TokenInterface $token
         */
        $token = $request->getToken();
        $order = $this->prepareOrder($token, $model, $apiSecret);
        $action = $token->getTargetUrl();
        $options = $order;
        $options['key'] = $apiKey;
        $options = json_encode($options);
        $form = <<<HTML
<form id="form" action="$action" method="POST">
<input type="hidden" id="razorpay_payment_id" name="razorpay_payment_id" value="">
</form>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
	var options = $options;
	
	options.handler = function(response) {
		document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
		document.getElementById('form').submit();
	}
	
	options.modal = {
		ondismiss: function() {
			document.getElementById('razorpay_payment_id').value = 'null';	
			document.getElementById('form').submit();
		}
	}
	
	var rzp = new Razorpay(options);
	rzp.open();
</script>
HTML;
		throw new HttpResponse($form);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SetRazorpay &&
            $request->getModel() instanceof \ArrayObject
            ;
    }

    /**
     * @return RazorpayBridgeInterface
     */
    public function getRazorpayBridge()
    {
        return $this->razorpayBridge;
    }

    /**
     * @param RazorpayBridgeInterface $razorpayBridge
     */
    public function setRazorpayBridge($razorpayBridge)
    {
        $this->razorpayBridge = $razorpayBridge;
    }

    private function prepareOrder(TokenInterface $token, $model, $posId)
    {
		/** @var CustomerInterface $customer */
		$customer = $model['customer'];

        $order = [];
        $order['amount'] = $model['amount'];
        $order['prefill.name'] = $customer->getFullName();
        $order['prefill.email'] = $model['prefill.email'];
        $order['local'] = $model['locale'];

        return $order;
    }

    private function prepareScriptAttributes(array $order)
	{
		$attributes = [];

		foreach ($order as $fieldName => $value) {
			$attributes[$f] = 'data-' . $fieldName . '="' . htmlentities($value) . '"';
		}

		return implode(' ', $attributes);
	}

    /**
     * @param string $gatewayName
     * @param object $model
     *
     * @return TokenInterface
     */
    private function createNotifyToken($gatewayName, $model)
    {
        return $this->payum->getTokenFactory()->createNotifyToken(
            $gatewayName,
            $model
        );
    }
}
