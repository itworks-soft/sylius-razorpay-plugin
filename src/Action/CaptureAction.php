<?php

namespace ITWorks\SyliusRazorpayPlugin\Action;

use ITWorks\SyliusRazorpayPlugin\SetRazorpay;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;

/**
 * @author Hlib Synkovskyi <gleb.sinkovskiy@gmail.com>
 */
final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = $request->getModel();
        ArrayObject::ensureArrayObject($model);

        $order = $request->getFirstModel()->getOrder();
        $model['customer'] = $order->getCustomer();
        $model['locale'] = $this->getFallbackLocaleCode($order->getLocaleCode());

        $razorpayAction = $this->getRazorpayAction($request->getToken(), $model);

        $this->getGateway()->execute($razorpayAction);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }

    /**
     * @return \Payum\Core\GatewayInterface
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param TokenInterface $token
     * @param ArrayObject $model
     *
     * @return SetRazorpay
     */
    private function getRazorpayAction(TokenInterface $token, ArrayObject $model)
    {
        $razorpayAction = new SetRazorpay($token);
        $razorpayAction->setModel($model);

        return $razorpayAction;
    }

    private function getFallbackLocaleCode($localeCode)
    {
        return explode('_', $localeCode)[0];
    }
}
