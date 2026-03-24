<?php

namespace App\Stats;

use Symfony\Component\HttpFoundation\Request;

final class BotDetectorService
{
    /**
     * Keep this deliberately simple.
     */
    private const BOT_PATTERNS = [
        'bot',
        'crawler',
        'spider',
        'crawl',
        'slurp',
        'bingpreview',
        'facebookexternalhit',
        'meta-externalagent',
        'meta-externalfetcher',
        'whatsapp',
        'telegrambot',
        'slackbot',
        'linkedinbot',
        'embedly',
        'quora link preview',
        'google page speed',
        'googlebot',
        'bingbot',
        'yandex',
        'duckduckbot',
        'baiduspider',
        'applebot',
        'semrushbot',
        'ahrefsbot',
        'mj12bot',
    ];

    public function isBot(Request $request): bool
    {
        $userAgent = trim((string) $request->headers->get('user-agent'));

        if ($userAgent === '') {
            return true;
        }

        if (empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            return true;
        }

        $normalized = mb_strtolower($userAgent);

        foreach (self::BOT_PATTERNS as $pattern) {
            if (str_contains($normalized, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
