<?php

namespace App\Stats;

use GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Request;

final class GeoIpService
{
    private ?Reader $reader = null;

    public function __construct(
        private readonly string $geoipDatabasePath,
    ) {}

    public function locate(Request $request): GeoIpResult
    {
        $ip = $request->getClientIp();

        if (!$ip) {
            return new GeoIpResult(null, null);
        }

        // Optional: skip local/dev IPs
        if (!$this->isPublicIp($ip)) {
            return new GeoIpResult(null, null);
        }

        try {

            $city = $this->getReader()->city($ip);

            return new GeoIpResult(
                countryCode: $city->country->isoCode ?: null,
                cityName: $city->city->name ?: null,
            );
        } catch (\Throwable $e) {

            return new GeoIpResult(null, null);
        }
    }

    private function getReader(): Reader
    {
        if ($this->reader instanceof Reader) {
            return $this->reader;
        }

        return $this->reader = new Reader($this->geoipDatabasePath);
    }

    private function isPublicIp(string $ip): bool
    {
        return false !== filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
