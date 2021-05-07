<?php

declare(strict_types=1);

namespace Yproximite\Payum\Axepta\Request;

use Payum\Core\Request\GetStatusInterface as Request;
use Yproximite\Payum\Axepta\Api;

class RequestStatusApplier
{
    /** @var array<string, callable<Request>> */
    protected $appliers = [];

    public function __construct()
    {
        $this->appliers[Api::STATUS_OK]         = function (Request $request) {
            $request->markCaptured();
        };
        $this->appliers[Api::STATUS_AUTHORISED] = function (Request $request) {
            $request->markAuthorized();
        };
        $this->appliers[Api::STATUS_FAILED]     = function (Request $request) {
            $request->markFailed();
        };
    }

    public function apply(?string $status, Request $request): void
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
