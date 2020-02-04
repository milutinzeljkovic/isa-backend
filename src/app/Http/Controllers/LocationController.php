<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function searchLocation(Request $request)
    {
        $location = $request->query('location');
        $apiKey = env('GOOGLE_API_KEY',null);

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://maps.googleapis.com/maps/api/place/textsearch/json?query='.$location.'&key='.(string)$apiKey,
            // You can set any number of default request options.
            'timeout'  => 10.0,
        ]);

        $response = $client->request('GET', '');
        $body = $response->getBody();
        //$stringBody = (string) $body;
        return $body;
    }
}