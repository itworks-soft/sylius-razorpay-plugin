<?php

namespace ITWorks\SyliusRazorpayPlugin;

use ITWorks\SyliusRazorpayPlugin\Action\CaptureAction;
use ITWorks\SyliusRazorpayPlugin\Action\ConvertPaymentAction;
use ITWorks\SyliusRazorpayPlugin\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

/**
 * @author Hlib Synkovskyi <gleb.sinkovskiy@gmail.com>
 */
final class RazorpayGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'razorpay',
            'payum.factory_title' => 'Razorpay',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.status' => new StatusAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'api_key' => '',
                'api_secret' => ''
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['api_key', 'api_secret'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $razorpayConfig = [
                    'api_key' => $config['api_key'],
                    'api_secret' => $config['api_secret'],
                ];

                return $razorpayConfig;
            };
        }
    }
}
