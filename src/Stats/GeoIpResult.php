<?php

namespace App\Stats;

final class GeoIpResult
{
    public function __construct(
        public readonly ?string $countryCode,
        public readonly ?string $cityName,
    ) {
    }
}