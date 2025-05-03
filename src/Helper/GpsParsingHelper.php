<?php

namespace App\Helper;

/**
 * PArse and convert EXIF degres lat/ng and to decimal lat/lng
 */
class GpsParsingHelper
{
    public function getGpsDecimal(array $coord, string $hemisphere): float
    {
        // Convert EXIF format (e.g. [deg, min, sec]) to decimal
        $degrees = $this->_gpsPartToFloat($coord[0]);
        $minutes = $this->_gpsPartToFloat($coord[1]);
        $seconds = $this->_gpsPartToFloat($coord[2]);

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        // Adjust sign based on hemisphere (S or W are negative)
        if ($hemisphere === 'S' || $hemisphere === 'W') {
            $decimal *= -1;
        }

        return $decimal;
    }

    private function _gpsPartToFloat(string $part): float
    {
        if (str_contains($part, '/')) {
            [$num, $den] = explode('/', $part);
            return (float)$num / (float)$den;
        }
        return (float)$part;
    }
}