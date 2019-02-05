<?php

namespace ITWorks\SyliusRazorpayPlugin\Action;

use ITWorks\SyliusRazorpayPlugin\Bridge\RazorpayBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

/**
 * @author Hlib Synkovskyi <gleb.sinkovskiy@gmail.com>
 */
final class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request GetStatusInterface */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = isset($model['razorpayStatus']) ? $model['razorpayStatus'] : null;

        if ((null === $status || RazorpayBridgeInterface::CREATED_API_STATUS === $status) && false === isset($model['orderId'])) {
            $request->markNew();
            return;
        }

        if (RazorpayBridgeInterface::FAILED_API_STATUS === $status) {
            $request->markFailed();
            return;
        }

        if (RazorpayBridgeInterface::CAPTURED_API_STATUS === $status) {
            $request->markCaptured();
            return;
        }

		if (RazorpayBridgeInterface::REFUNDED_API_STATUS === $status) {
			$request->markRefunded();
			return;
		}

        $request->markUnknown();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
