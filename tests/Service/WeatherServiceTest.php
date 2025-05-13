<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Client\WeatherClient;
use App\Service\WeatherService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Psr\Log\NullLogger;

class WeatherServiceTest extends TestCase
{
    public function testGetWeatherReturnsExpectedData(): void
    {
        $json = <<<'JSON'
{
  "location": {
    "name": "Berlin",
    "country": "Germany"
  },
  "current": {
    "temp_c": 18.5,
    "humidity": 55,
    "condition": { "text": "Sunny" },
    "wind_kph": 12.3,
    "last_updated": "2025-05-11 15:30"
  }
}
JSON;

        $mockResponse = new MockResponse($json, [
            'http_code' => 200,
            'response_headers' => ['content-type' => 'application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);
        $logger = new NullLogger();
        $client = new WeatherClient($httpClient, 'fake-key', 'fake-url');
        $service = new WeatherService($logger, $client);

        $weather = $service->getWeather('Berlin');

        $this->assertIsArray($weather);
        $this->assertArrayHasKey('city', $weather);
        $this->assertArrayHasKey('temperature', $weather);
        $this->assertSame('Berlin', $weather['city']);
        $this->assertSame('Germany', $weather['country']);
        $this->assertIsFloat($weather['temperature']);
        $this->assertIsInt($weather['humidity']);
        $this->assertIsFloat($weather['windSpeed']);

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/',
            $weather['lastUpdated']
        );

        $this->assertSame('Sunny', $weather['condition']);
    }
}
