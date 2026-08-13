<?php

declare(strict_types=1);

namespace Yproximite\Payum\Axepta\Tests;

class AxeptaGatewayFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function shouldSubClassGatewayFactory()
    {
        $rc = new \ReflectionClass('Yproximite\Payum\Axepta\AxeptaGatewayFactory');

        $this->assertTrue($rc->isSubclassOf('Payum\Core\GatewayFactory'));
    }

    /**
     * @test
     */
    public function shouldCreateGatewayFromRequiredOptions()
    {
        $gateway = (new \Yproximite\Payum\Axepta\AxeptaGatewayFactory())->create([
            'merchant_id' => 'TESTMERCHANT',
            'hmac'        => '0123456789abcdef0123456789abcdef',
            'crypt_key'   => '0123456789abcdef',
        ]);

        $this->assertInstanceOf('Payum\Core\GatewayInterface', $gateway);
    }
}
