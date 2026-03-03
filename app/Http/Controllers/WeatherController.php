<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:100',
        ]);

        $apiKey = env('OPENWEATHER_API_KEY');
        $city = $request->input('city');

        try {
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang' => 'pt_br'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'city' => $data['name'],
                        'country' => $data['sys']['country'],
                        'temperature' => $data['main']['temp'],
                        'feels_like' => $data['main']['feels_like'],
                        'humidity' => $data['main']['humidity'],
                        'description' => $data['weather'][0]['description'],
                        'wind_speed' => $data['wind']['speed'],
                        // Probabilidade de chuva (Não disponivel no plano da API atual "Free")
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Cidade não encontrada'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados do clima'
            ], 500);
        }
    }
}
