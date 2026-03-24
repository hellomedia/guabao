<?php

namespace App\EventSubscriber;

use App\Stats\AnonymousTrackingService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AnonymousTrackingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AnonymousTrackingService $trackingService,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->trackingService->shouldTrack($event)) {
            return;
        }

        $this->trackingService->track($request);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
            
        $request = $event->getRequest();
        
        $visitorId = $this->trackingService->getNewVisitorIdToSetAsCookie($request);

        if (!$visitorId) {
            return;
        }

        $cookie = Cookie::create(
            name: AnonymousTrackingService::VISITOR_COOKIE_NAME,
            value: $visitorId,
            expire: new \DateTimeImmutable('+12 months'),
            path: '/',
            secure: $request->isSecure(),
            httpOnly: true,
            raw: false,
            sameSite: Cookie::SAMESITE_LAX,
        );

        $event->getResponse()->headers->setCookie($cookie);
    }
}
