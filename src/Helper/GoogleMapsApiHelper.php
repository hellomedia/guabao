<?php

namespace App\Helper;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleMapsApiHelper
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $googleBackendApiKey,
    ) {}

    /**
     * Geocoding API
     * does not have businesses (more administrative buildings etc)
     * API call returns a list of nearby places (around 20), ordered by proximlity
     * We return the first / closest result
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        $url = sprintf(
            'https://maps.googleapis.com/maps/api/geocode/json?latlng=%f,%f&key=%s',
            $lat,
            $lng,
            $this->googleBackendApiKey
        );

        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray(false);

        if (($data['status'] ?? '') !== 'OK') {
            return null;
        }

        return [
            'place_id' => $data['results'][0]['place_id'] ?? null,
            'formatted_address' => $data['results'][0]['formatted_address'] ?? null,
            'address_components' => $data['results'][0]['address_components'] ?? [],
        ];
    }

    /**
     * Places API
     * for shops, restaurants
     * API call returns a list of nearby places (around 20), ordered by proximlity
     * We return the first / closest result
     */
    public function findNearbyPlace(float $lat, float $lng, int $radius = 50): ?array
    {
        $url = sprintf(
            'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=%f,%f&radius=%d&key=%s',
            $lat,
            $lng,
            $radius,
            $this->googleBackendApiKey
        );

        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray(false);

        if (($data['status'] ?? '') !== 'OK' || empty($data['results'])) {
            return null;
        }

        return $data['results'][0]; // nearest place
    }
}
