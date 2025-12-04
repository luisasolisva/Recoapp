<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Cliente HTTP que centraliza llamadas a la API externa.
 * Se usa Guzzle para conexiones resilientes y se agregan logs para depurar errores.
 */
class ExternalApiService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('external-api.base_url'),
            'timeout' => config('external-api.timeout'),
            'http_errors' => false
        ]);
    }

    /** @return array<int, mixed> */
    public function listVehiculos(string $perfilId): array
    {
        return $this->get('/vehiculos', ['perfil_id' => $perfilId]);
    }

    public function getVehiculo(string $id, string $perfilId): array
    {
        return $this->get("/vehiculos/{$id}", ['perfil_id' => $perfilId]);
    }

    public function findVehiculoByPlaca(string $placa, string $perfilId): ?array
    {
        $vehiculos = $this->listVehiculos($perfilId);
        return collect($vehiculos)->firstWhere('placa', $placa);
    }

    public function registerVehiculo(array $payload): array
    {
        return $this->post('/vehiculos', $payload);
    }

    public function updateVehiculo(string $id, array $payload): array
    {
        return $this->put("/vehiculos/{$id}", $payload);
    }

    public function deleteVehiculo(string $id, string $perfilId): void
    {
        $this->delete("/vehiculos/{$id}", ['perfil_id' => $perfilId]);
    }

    public function listRutas(string $perfilId): array
    {
        return $this->get('/rutas', ['perfil_id' => $perfilId]);
    }

    public function getRuta(string $id, string $perfilId): array
    {
        return $this->get("/rutas/{$id}", ['perfil_id' => $perfilId]);
    }

    public function createRuta(array $payload): array
    {
        return $this->post('/rutas', $payload);
    }

    public function updateRuta(string $id, array $payload): array
    {
        return $this->put("/rutas/{$id}", $payload);
    }

    public function deleteRuta(string $id, string $perfilId): void
    {
        $this->delete("/rutas/{$id}", ['perfil_id' => $perfilId]);
    }

    public function listRecorridos(string $perfilId): array
    {
        return $this->get('/misrecorridos', ['perfil_id' => $perfilId]);
    }

    public function startRecorrido(array $payload): array
    {
        return $this->post('/recorridos/iniciar', $payload);
    }

    public function finishRecorrido(string $recorridoId): void
    {
        $this->post("/recorridos/{$recorridoId}/finalizar");
    }

    public function storePosition(string $recorridoId, array $payload): ?array
    {
        try {
            return $this->post("/recorridos/{$recorridoId}/posiciones", $payload);
        } catch (\RuntimeException $exception) {
            Log::warning('Falla enviando posición, se encola para reintento', [
                'recorrido' => $recorridoId,
                'payload' => $payload,
                'error' => $exception->getMessage()
            ]);
            return null;
        }
    }

    public function getPositions(string $recorridoId, string $perfilId): array
    {
        return $this->get("/recorridos/{$recorridoId}/posiciones", ['perfil_id' => $perfilId]);
    }

    /**
     * Métodos privados para homogeneizar manejo de errores.
     */
    private function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    private function post(string $endpoint, array $payload = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $payload]);
    }

    private function put(string $endpoint, array $payload = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $payload]);
    }

    private function delete(string $endpoint, array $query = []): array
    {
        return $this->request('DELETE', $endpoint, ['query' => $query]);
    }

    private function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            $status = $response->getStatusCode();

            if ($status >= 200 && $status < 300) {
                $body = (string) $response->getBody();
                return $body !== '' ? json_decode($body, true, flags: JSON_THROW_ON_ERROR) : [];
            }

            throw new \RuntimeException("Error {$status} en API externa: {$response->getBody()} ");
        } catch (GuzzleException $exception) {
            throw new \RuntimeException('No se pudo contactar la API externa: '.$exception->getMessage(), 0, $exception);
        }
    }
}
