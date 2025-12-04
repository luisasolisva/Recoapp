<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test básico para comprobar que la ruta raíz responde.
 */
class ExampleTest extends TestCase
{
    public function test_root_endpoint_returns_metadata(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
