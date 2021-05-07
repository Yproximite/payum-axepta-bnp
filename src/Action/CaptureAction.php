<?php

declare(strict_types=1);

namespace Yproximite\Payum\Axepta\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Yproximite\Payum\Axepta\Action\Api\BaseApiAwareAction;
use Yproximite\Payum\Axepta\Api;

class CaptureAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface, GenericTokenFactoryAwareInterface
{
    use GatewayAwareTrait;
    use GenericTokenFactoryAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (null === $details[Api::FIELD_VADS_URL_SUCCESS] && $request->getToken() instanceof TokenInterface) {
            $notifyToken = $this->tokenFactory->createNotifyToken(
                $request->getToken()->getGatewayName(),
                $request->getToken()->getDetails()
            );

            $details[Api::FIELD_VADS_URL_NOTIFY] = $notifyToken->getTargetUrl();
        }

        if (null === $details[Api::FIELD_VADS_URL_BACK] && $request->getToken() instanceof TokenInterface) {
            $details[Api::FIELD_VADS_URL_SUCCESS] = $request->getToken()->getAfterUrl();
            $details[Api::FIELD_VADS_URL_FAILURE] = $request->getToken()->getAfterUrl();
            $details[Api::FIELD_VADS_URL_BACK]    = $request->getToken()->getAfterUrl();
        }

        $this->api->doPayment((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
