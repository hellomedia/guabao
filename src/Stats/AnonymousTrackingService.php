<?php

namespace App\Stats;

use App\Entity\Stats\AnonymousPageView;
use App\Entity\Stats\AnonymousVisit;
use App\Repository\Stats\AnonymousVisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Uid\Uuid;

final class AnonymousTrackingService
{
    public const VISITOR_COOKIE_NAME = 'visitor_id';
    private const SESSION_VISIT_ID_KEY = 'stats.anonymous_visit_id';
    private const REQUEST_NEW_VISITOR_ID_ATTR = 'stats.new_visitor_id';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AnonymousVisitRepository $visitRepository,
        private readonly GeoIpService $geoIpService,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly BotDetectorService $botDetector,
    ) {}

    public function shouldTrack(RequestEvent $event): bool
    {
        if (!$event->isMainRequest()) {
            return false;
        }

        $request = $event->getRequest();

        if ($this->botDetector->isBot($request)) {
            return false;
        }
    
        if ($this->isBroswerPrefetch($request)) {
            return false;
        }

        if ('GET' !== $request->getMethod()) {
            return false;
        }

        if ($this->security->getUser()) {
            return false;
        }

        $path = $request->getPathInfo();

        if (
            str_starts_with($path, '/_wdt')
            || str_starts_with($path, '/_profiler')
            || str_starts_with($path, '/admin')
            || str_starts_with($path, '/build')
            || str_starts_with($path, '/bundles')
        ) {
            return false;
        }

        return true;
    }

    public function track(Request $request): void
    {
        $session = $this->getSession($request);
        $session->start();

        $now = new \DateTimeImmutable();

        $visitorId = $request->cookies->get(self::VISITOR_COOKIE_NAME);
        $isNewVisitorCookie = false;

        if (!$visitorId) {
            $visitorId = Uuid::v4()->toRfc4122();
            $isNewVisitorCookie = true;
            $request->attributes->set(self::REQUEST_NEW_VISITOR_ID_ATTR, $visitorId);
        }

        $visit = $this->getOrCreateVisit(
            request: $request,
            session: $session,
            visitorId: $visitorId,
            now: $now,
        );

        $pageView = new AnonymousPageView();
        $pageView->setVisit($visit);
        $pageView->setVisitedAt($now);
        $pageView->setPath($request->getPathInfo());
        $pageView->setRouteName($request->attributes->get('_route'));
        $pageView->setQueryString($request->getQueryString());
        $pageView->setReferrer($request->headers->get('referer'));

        $visit->setLastSeenAt($now);
        $visit->incrementPageCount();

        $this->entityManager->persist($pageView);
        $this->entityManager->flush();
    }

    public function getNewVisitorIdToSetAsCookie(Request $request): ?string
    {
        return $request->attributes->get(self::REQUEST_NEW_VISITOR_ID_ATTR);
    }

    private function getOrCreateVisit(
        Request $request,
        SessionInterface $session,
        string $visitorId,
        \DateTimeImmutable $now,
    ): AnonymousVisit {
        $visitId = $session->get(self::SESSION_VISIT_ID_KEY);

        if ($visitId) {
            $visit = $this->visitRepository->find($visitId);

            if ($visit instanceof AnonymousVisit) {
                return $visit;
            }
        }

        $geo = $this->geoIpService->locate($request);

        $visit = new AnonymousVisit();
        $visit->setSessionId($session->getId());
        $visit->setVisitorId($visitorId);
        $visit->setStartedAt($now);
        $visit->setLastSeenAt($now);
        $visit->setPageCount(0);
        $visit->setIsReturning($this->visitRepository->hasPreviousVisitForVisitorId($visitorId));
        $visit->setCountryCode($geo->countryCode);
        $visit->setCityName($geo->cityName);
        $visit->setFirstPath($request->getPathInfo());
        $visit->setLandingReferrer($request->headers->get('referer'));
        $visit->setUserAgent($request->headers->get('user-agent'));

        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        $session->set(self::SESSION_VISIT_ID_KEY, $visit->getId());

        return $visit;
    }

    private function getSession(Request $request): SessionInterface
    {
        if (!$request->hasSession()) {
            throw new \LogicException('Anonymous tracking requires Symfony session to be enabled.');
        }

        return $request->getSession();
    }

    private function isBroswerPrefetch(Request $request): bool
    {
        // skip browser prefetch / prerender traffic
        $purpose = mb_strtolower((string) $request->headers->get('purpose'));
        if (in_array($purpose, ['prefetch', 'prerender'], true)) {
            return true;
        }

        $secPurpose = mb_strtolower((string) $request->headers->get('sec-purpose'));
        if (str_contains($secPurpose, 'prefetch')) {
            return true;
        }

        return false;
    }
}
