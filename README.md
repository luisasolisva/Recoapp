# Backend Laravel

Este directorio contiene un esqueleto autocommentado de Laravel optimizado para actuar como *backend BFF* entre la app Ionic y la API externa de recolección. Todos los archivos incluyen comentarios detallados para facilitar el estudio del flujo interno.

## Requisitos

- PHP 8.3+
- Composer 2.7+
- Extensiones: `pdo_mysql`, `curl`, `mbstring`, `openssl`
- Base de datos MySQL (por defecto) o PostgreSQL

## Puesta en marcha

1. Instala dependencias de Composer en esta carpeta:
	```bash
	cd backend-laravel
	composer install
	```
2. Copia variables de entorno y ajusta credenciales/API keys:
	```bash
	cp .env.example .env
	php artisan key:generate
	```
3. Configura la base de datos en `.env` y ejecuta migraciones:
	```bash
	php artisan migrate
	```
4. Opcional: ejecuta el servidor embebido
	```bash
	php artisan serve --host=0.0.0.0 --port=8000
	```

## Estructura destacada

- `app/Services/ExternalApiService.php` centraliza el consumo de la API externa con manejo de errores.
- `app/Services/TrackingQueueService.php` almacena posiciones fallidas y las reintenta (trigger manual o con `artisan tracking:flush`).
- `app/Http/Controllers/*` exponen endpoints REST protegidos con Sanctum.
- `database/migrations/*` generan tablas para usuarios, tokens, trabajos en cola y la *queue* local de posiciones.
- `config/external-api.php` parametriza base URL, timeout y `GLOBAL_PERFIL_ID` de respaldo para modo ciudadano.

Consulta los comentarios de cada archivo para entender paso a paso las responsabilidades.

```
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
