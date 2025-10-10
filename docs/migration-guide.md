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

## 3.1. Verificar extensiones PDO
Laravel necesita que la extensión PDO del motor que configuraste esté instalada en PHP. Comprueba qué driver usarás en `DB_CONNECTION` y habilita el módulo correspondiente:

| Driver (`DB_CONNECTION`) | Extensión PHP | Ubuntu/Debian | macOS (Homebrew) |
|--------------------------|---------------|---------------|------------------|
| `mysql`                  | `pdo_mysql`   | `sudo apt install php-mysql` | `brew install php` *(incluye pdo_mysql)* |
| `pgsql`                  | `pdo_pgsql`   | `sudo apt install php-pgsql` | `brew install php` *(incluye pdo_pgsql)* |
| `sqlsrv`                 | `pdo_sqlsrv`  | [Extensiones Microsoft](https://learn.microsoft.com/sql/connect/php/installation-tutorial-linux-mac) | `pecl install sqlsrv pdo_sqlsrv` |
| `sqlite`                 | `pdo_sqlite`  | `sudo apt install php-sqlite3` | `brew install php` *(incluye pdo_sqlite)* |

Tras instalar la extensión, reinicia el servicio de PHP/FPM o tu terminal antes de volver a lanzar `php artisan migrate`.


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
