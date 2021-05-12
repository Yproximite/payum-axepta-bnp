<?php

declare(strict_types=1);

namespace Yproximite\Payum\Axepta\Request;

use Payum\Core\Request\GetStatusInterface;
use Yproximite\Payum\Axepta\Api;

class RequestStatusApplier
{
    /** @var array<string, callable(GetStatusInterface): void> */
    protected array $appliers = [];

    public function __construct()
    {
        $this->appliers[Api::STATUS_OK]         = function (GetStatusInterface $request): void {
            $request->markCaptured();
        };
        $this->appliers[Api::STATUS_AUTHORISED] = function (GetStatusInterface $request): void {
            $request->markAuthorized();
        };
        $this->appliers[Api::STATUS_FAILED]     = function (GetStatusInterface $request): void {
            $request->markFailed();
        };
    }

    public function apply(?string $status, GetStatusInterface $request): void
    {
        if (null === $status) {
            $request->markNew();

            return;
        }

        if (!array_key_exists($status, $this->appliers)) {
            $request->markUnknown();

            return;
        }

        $this->appliers[$status]($request);
    }
}
