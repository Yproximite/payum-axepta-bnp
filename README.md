# Payum Axepta by BNP

> A Payum gateway to use [Axepta](https://docs.axepta.bnpparibas/display/DOCBNP/Documentation+Axepta+BNP+Paribas+-++Home) (a French payment system)

[![Latest Stable Version](https://poser.pugx.org/yproximite/payum-axepta-bnp/version)](https://packagist.org/packages/yproximite/payum-axepta-bnp)
[![Build Status](https://travis-ci.com/Yproximite/payum-axepta-bnp.svg?token=pNBs2oaRpfxdyhqWf28h&branch=master)](https://travis-ci.com/Yproximite/payum-axepta-bnp)

## Requirements

- PHP 7.4+
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
    arguments: [ Yproximite\Payum\Axepta\AxeptaGatewayFactory ]
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
      merchant_id: 'change it' # required
      hmac: 'change it' # required
      crypt_key: 'change it' # required
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
        'factory'     => 'axepta',
        'merchant_id' => 'change it', // required
        'hmac'        => 'change it', // required
        'crypt_key'   => 'change it', // required
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

By doing this, the library will be able to pick the payment's id and use it for the payment with Axepta.
