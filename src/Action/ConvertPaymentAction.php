<?php

declare(strict_types=1);

namespace Yproximite\Payum\Axepta\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Request\GetCurrency;
use Yproximite\Payum\Axepta\Api;

class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $details[Api::FIELD_VADS_TRANS_ID]       = sprintf('%012d', $payment->getNumber());
        $details[Api::FIELD_VADS_REF_NR]         = sprintf('%012d', $payment->getNumber());
        $details[Api::FIELD_VADS_USER_DATA]      = $payment->getNumber();
        $details[Api::FIELD_VADS_AMOUNT]         = $payment->getTotalAmount();

        $this->gateway->execute($currency = new GetCurrency($payment->getCurrencyCode()));
        $details[Api::FIELD_VADS_CURRENCY] = $currency->code;

        $details[Api::FIELD_VADS_RESPONSE]   = 'encrypt';
        $details[Api::FIELD_VADS_LANGUAGE]   = $payment->getDetails()[Api::FIELD_VADS_LANGUAGE] ?? Api::LANGUAGE_FR;
        $details[Api::FIELD_VADS_ORDER_DESC] = $payment->getDetails()[Api::FIELD_VADS_ORDER_DESC] ?? 'Description';

        $request->setResult((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            'array' === $request->getTo();
    }
}
