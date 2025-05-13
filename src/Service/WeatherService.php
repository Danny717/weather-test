<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\WeatherClient;
use Psr\Log\LoggerInterface;

readonly class WeatherService
{
    public function __construct(
        private LoggerInterface $weatherLogger,
        private WeatherClient   $client,
    )
    {
    }

    public function getWeather(string $city): array
    {
        // get response from client
        $response = $this->client->getWeather($city);

        // check if response status not OK
        if ($response->getStatusCode() !== 200) {
            $this->weatherLogger->error('Error while getting weather data');
            throw new \Exception('Error while getting weather data');
        };

        $content = $response->toArray();

        //check if response content has error
        if (isset($content['error'])) {
            $this->weatherLogger->error($content['error']['message']);
            throw new \Exception('ERROR: ' . $content['error']['message']);
        }

        //write in log file
        $this->weatherLogger->info(
            date('Y-m-d H:i:s')
            . " - Weather in {$content['location']['name']}: {$content['current']['temp_c']}°C, "
            . "{$content['current']['condition']['text']}"
        );

        // we can set default return values like $content['location']['name'] ?? 'Default value',
        // but we trust client:)
        return [
            'city' => $content['location']['name'],
            'country' => $content['location']['country'],
            'temperature' => $content['current']['temp_c'],
            'humidity' => $content['current']['humidity'],
            'condition' => $content['current']['condition']['text'],
            'windSpeed' => $content['current']['wind_kph'],
            'lastUpdated' => $content['current']['last_updated'],
        ];
    }
}