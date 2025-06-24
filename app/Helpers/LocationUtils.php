<?php

namespace App\Helpers;

class LocationUtils
{
    public static function calculateDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $unit = 'km')
    {
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $nauticalMiles = $miles * 0.8684;

        switch ($unit) {
            case 'km':
                return $kilometers;
            case 'nmi':
                return $nauticalMiles;
            case 'm':
                return $kilometers * 1000;
            default:
                return $miles;
        }
    }

}