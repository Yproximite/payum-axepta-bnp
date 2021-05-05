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
}
