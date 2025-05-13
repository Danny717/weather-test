<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}', name: 'weather')]
    public function getWeatherData(WeatherService $weatherService, string $city): Response
    {
        try {
            $weather = $weatherService->getWeather($city);
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }

        return $this->render('weather.html.twig', [
            'weather' => $weather,
        ]);
    }
}