# Guía para ejecutar `php artisan migrate`

Sigue estos pasos para ejecutar las migraciones de la aplicación en un entorno local basado en PHP y Composer.

## 1. Instalar dependencias
```bash
composer install
```

## 2. Configurar el archivo `.env`
Crea el archivo de entorno a partir del ejemplo y actualiza las credenciales de base de datos (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
```bash
cp .env.example .env
php artisan key:generate
```

## 3. Crear la base de datos (si no existe)
Asegúrate de que la base de datos definida en `.env` exista antes de correr las migraciones. Puedes crearla desde tu gestor favorito o con el cliente de MySQL/PostgreSQL, por ejemplo:
```bash
mysql -u root -p -e "CREATE DATABASE restaurante;"
```

## 4. Ejecutar las migraciones
Con la base de datos lista, ejecuta:
```bash
php artisan migrate
```

Si necesitas reconstruir la base de datos desde cero, utiliza:
```bash
php artisan migrate:fresh --seed
```

## Uso con Docker / Sail
Si utilizas Laravel Sail u otro contenedor donde los comandos se ejecutan dentro del servicio de aplicación, antepone `./vendor/bin/sail` u otro wrapper equivalente:
```bash
./vendor/bin/sail artisan migrate
```
