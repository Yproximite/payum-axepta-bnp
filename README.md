# Payum Axepta by BNP

> A Payum gateway to use [Axepta](https://docs.axepta.bnpparibas/display/DOCBNP/Documentation+Axepta+BNP+Paribas+-++Home) (a French payment system)

[![Latest Stable Version](https://poser.pugx.org/yproximite/payum-axepta-bnp/version)](https://packagist.org/packages/yproximite/payum-axepta-bnp)
[![Build Status](https://travis-ci.com/Yproximite/payum-axepta-bnp.svg?token=pNBs2oaRpfxdyhqWf28h&branch=master)](https://travis-ci.com/Yproximite/payum-axepta-bnp)

## Requirements

- PHP 7.2+
- [Payum](https://github.com/Payum/Payum)
- Optionally [PayumBundle](https://github.com/Payum/PayumBundle) and Symfony 3 or 4+

## Installation

```bash
$ composer require yproximite/payum-axepta-bnp
```

## Configuration

### With PayumBundle (Symfony)

First register the gateway factory in your services definition:
```yaml
# config/services.yaml or app/config/services.yml
services:
    yproximite.axepta_gateway_factory:
        class: Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder
        arguments: [Yproximite\Payum\Axepta\AxeptaGatewayFactory]
        tags:
            - { name: payum.gateway_factory_builder, factory: axepta }
```

Then configure the gateway:

```yaml
# config/packages/payum.yaml or app/config/config.yml

payum:
  gateways:
    axepta:
      factory: axepta
```

### With Payum

```php
<?php
//config.php

use Payum\Core\PayumBuilder;
use Payum\Core\Payum;

/** @var Payum $payum */
$payum = (new PayumBuilder())
    ->addDefaultStorages()

    ->addGateway('gatewayName', [
        'factory' => 'axepta',
    ])

    ->getPayum()
;
```

## Usage

Make sure your `Payment` entity overrides `getNumber()` method like this:
```php
<?php

namespace App\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class Payment extends BasePayment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getNumber()
    {
        return (string) $this->id;
    }
}
```

By doing this, the library will be able to pick the payment's id and use it for the payment with System Pay (we should send a transaction id between `000000` and `999999`).

### Payment in several installments

If you planned to support payments in several instalments, somewhere in your code you will need to call `Payment#setPartialAmount` to keep a trace of the amount per payment:

```php
<?php
class Payment extends BasePayment
{
    // ...

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $partialAmount;

    public function getPartialAmount(): ?int
    {
        return $this->partialAmount;
    }

    public function setPartialAmount(?int $partialAmount): void
    {
        $this->partialAmount = $partialAmount;
    }
}
```

#### Usage

```php
<?php

use App\Entity\Payment;
use Yproximite\Payum\Axepta\Api;
use Yproximite\Payum\Axepta\PaymentConfigGenerator;

// Define the periods
$periods = [
    ['amount' => 1000, 'date' => new \DateTime()],
    ['amount' => 2000, 'date' => (new \DateTime())->add(new \DateInterval('P1M'))],
    ['amount' => 3000, 'date' => (new \DateTime())->add(new \DateInterval('P2M'))],
];

// Compute total amount
$totalAmount = array_sum(array_column($periods, 'amount'));

// Compute `paymentConfig` fields that will be sent to the API
// It will generates something like this: MULTI_EXT:20190102=1000;20190202=2000;20190302=3000
$paymentConfig = (new PaymentConfigGenerator())->generate($periods);

// Then create payments
$storage = $payum->getStorage(Payment::class);
$payments = [];

foreach ($periods as $period) {
    $payment = $storage->create();
    $payment->setTotalAmount($totalAmount);
    $payment->setPartialAmount($period['amount']);

    $details = $payment->getDetails();
    $details[Api::FIELD_VADS_PAYMENT_CONFIG] = $generatedPaymentConfig;
    $payment->setDetails($details);

    $storage->update($payment);
    $payments[] = $payment;
}
```
