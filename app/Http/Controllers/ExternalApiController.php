<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;              // ✅ Importar Request
use GuzzleHttp\Client;                   // ✅ Importar Client de Guzzle
use Illuminate\Support\Facades\Log;       // ✅ Importar Log

class ExternalApiController extends Controller   // ✅ Extiende Controller
{
    public function proxy(Request $request, $path = null)
    {
        // Base URL sin duplicar /api
        $externalBaseUrl = 'https://apirecoleccion.gonzaloandreslucio.com/';

        // Construir URL final
        $url = $externalBaseUrl . 'api/' . ltrim($path, '/');

        // Log para depuración
        Log::info("Path recibido: " . $path);
        Log::info("URL final: " . $url);

        $client = new Client();

        try {
            // 🔹 Traducción de placa a vehiculo_id
            if ($path === 'recorridos/iniciar' && $request->has('placa')) {
                if (!$request->filled('perfil_id')) {
                    return response()->json(['message' => 'perfil_id es requerido'], 422);
                }

                $vehiculosResp = $client->get($externalBaseUrl . 'api/vehiculos', [
                    'query' => ['perfil_id' => $request->input('perfil_id')]
                ]);
                $vehiculos = json_decode($vehiculosResp->getBody(), true)['data'] ?? [];

                $vehiculo = collect($vehiculos)->firstWhere('placa', $request->input('placa'));
                if (!$vehiculo) {
                    return response()->json(['message' => 'No se encontró vehículo con esa placa'], 422);
                }

                // Reemplazar placa por vehiculo_id
                $request->merge(['vehiculo_id' => $vehiculo['id']]);
                $request->request->remove('placa');
            }

            // 🔹 Validar campos requeridos
            if ($path === 'recorridos/iniciar') {
                if (!$request->filled('ruta_id') || !$request->filled('perfil_id') || !$request->filled('vehiculo_id')) {
                    return response()->json(['message' => 'ruta_id, vehiculo_id y perfil_id son requeridos'], 422);
                }
            }

            // Reenvío estándar
            $response = $client->request(
                $request->method(),
                $url,
                [
                    'query' => $request->query(),
                    'json'  => $request->all(),
                    'headers' => ['Accept' => 'application/json'],
                    'verify' => false
                ]
            );

            return response($response->getBody(), $response->getStatusCode())
                ->withHeaders([
                    'Content-Type' => $response->getHeader('Content-Type')[0] ?? 'application/json',
                ]);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 503;
            $body = $e->hasResponse() ? (string)$e->getResponse()->getBody() : '{"error": "Error de conexión con la API externa."}';

            Log::error("Error en proxy a Lucio API. URL: " . $url . " | Status: " . $statusCode . " | Body: " . $body);

            return response($body, $statusCode)
                ->header('Content-Type', 'application/json');
        }
    }
}
