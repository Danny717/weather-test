<?php

declare(strict_types=1);

namespace App\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as Response;

class WeatherClient
{
    private const GET = 'GET';
    private const TIMEOUT = 30;
    private const URI = '/v1/current.json?';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
        private readonly string $url
    ){
    }

    public function getWeather(string $city): Response
    {
        // create query params
        $queryParams = [
            'q' => $city,
            'key' => $this->apiKey,
        ];

        //send request
        return $this->httpClient->request(
            self::GET,
            self::URI . http_build_query($queryParams),
            [
                'base_uri' => $this->url,
                'timeout' => self::TIMEOUT
            ]
        );
    }
}