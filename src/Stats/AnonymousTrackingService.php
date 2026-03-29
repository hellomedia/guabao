<?php

namespace App\Stats;

use App\Entity\Stats\AnonymousPageView;
use App\Entity\Stats\AnonymousVisit;
use App\Entity\Stats\AnonymousVisitor;
use App\Repository\Stats\AnonymousVisitorRepository;
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
    // browser cookie name -- to track a visitor between sessions
    public const VISITOR_COOKIE_NAME = 'visitor_id';
    // key used inside symfony session
    private const SESSION_VISIT_ID_KEY = 'stats.anonymous_visit_id';
    // This one does not live in the browser or symfony session.
    // Only lives during current request, so the response subscriber knows it needs to set a cookie.
    // Temporary handoff between the tracking logic during request handling
    // and cookie-setting logic during response handling
    private const REQUEST_NEW_VISITOR_ID_ATTR = 'stats.new_visitor_id';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AnonymousVisitRepository $visitRepository,
        private readonly AnonymousVisitorRepository $visitorRepository,
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

        $visitor = $this->visitorRepository->findOneByVisitorId($visitorId);

        if (!$visitor) {
            $visitor = new AnonymousVisitor();
            $visitor->setVisitorId($visitorId);
            $visitor->setFirstSeenAt($now);
            $visitor->setLastSeenAt($now); // not null in DB ==> before flush
            $visitor->setUserAgent($request->headers->get('user-agent'));

            $this->entityManager->persist($visitor);
            $this->entityManager->flush();
        }

        $visit = $this->getOrCreateVisit(
            request: $request,
            session: $session,
            visitor: $visitor,
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

        $visitor->setLastSeenAt($now);
        $visitor->incrementPageCount();

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
        AnonymousVisitor $visitor,
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
        $visit->setVisitor($visitor);
        $visit->setStartedAt($now);
        $visit->setLastSeenAt($now);
        $visit->setPageCount(0);
        $visit->setIsReturning($this->visitRepository->hasPreviousVisitForVisitor($visitor));
        $visit->setCountryCode($geo->countryCode);
        $visit->setCityName($geo->cityName);
        $visit->setFirstPath($request->getPathInfo());
        $visit->setLandingReferrer($request->headers->get('referer'));
        $visit->setIp($request->getClientIp());

        $visitor->addVisit($visit);

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
