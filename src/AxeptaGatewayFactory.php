<?php

declare(strict_types=1);

namespace Yproximite\Payum\Axepta;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Yproximite\Payum\Axepta\Action\CaptureAction;
use Yproximite\Payum\Axepta\Action\ConvertPaymentAction;
use Yproximite\Payum\Axepta\Action\NotifyAction;
use Yproximite\Payum\Axepta\Action\StatusAction;
use Yproximite\Payum\Axepta\Request\RequestStatusApplier;

class AxeptaGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name'           => 'axepta',
            'payum.factory_title'          => 'axepta',
            'payum.action.capture'         => new CaptureAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.notify'          => new NotifyAction(),
            'payum.action.status'          => function (ArrayObject $config) {
                return new StatusAction($config['payum.request_status_applier']);
            },
            'payum.request_status_applier' => new RequestStatusApplier(),
        ]);

        if (false === ($config['payum.api'] ?? false)) {
            $config['payum.default_options'] = [
                'merchant_id' => null,
                'hmac'        => null,
                'crypt_key'   => null,
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'merchant_id',
                'hmac',
                'crypt_key',
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
