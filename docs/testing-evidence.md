# Guía paso a paso para ejecutar y documentar las pruebas

Esta guía explica cómo correr las suites automatizadas (caja negra y caja blanca) del proyecto y cómo generar evidencia reproducible de su ejecución.

## 1. Preparar el entorno

1. **Clonar el repositorio y entrar a la carpeta del proyecto.**
   ```bash
   git clone <url-del-repo>
   cd restaurante-laravel
   ```
2. **Configurar las variables de entorno necesarias.** Si todavía no existe, duplica `.env.example` y ajusta los valores mínimos (por ejemplo, `APP_KEY`). Para las pruebas no es necesario un servidor de base de datos porque se usa SQLite en memoria, pero sí debes tener instalado PHP 8.1+, Composer y SQLite.
3. **Instalar las dependencias PHP.**
   ```bash
   composer install
   ```
   - Si Composer muestra un error 403 al descargar desde GitHub, crea un [token personal](https://github.com/settings/tokens) y ejecútalo una vez:
     ```bash
     composer config -g github-oauth.github.com <tu-token>
     composer install
     ```

## 2. Ejecutar las pruebas automatizadas

La configuración de `phpunit.xml` ya define una base de datos SQLite en memoria, por lo que no debes crear archivos adicionales.

1. **Pruebas de caja negra (Feature tests).**
   ```bash
   php artisan test --testsuite=Feature
   ```
   Estas pruebas envían peticiones HTTP a los endpoints de pedidos y verifican las respuestas completas, tal como lo haría un cliente externo.

2. **Pruebas de caja blanca (Unit tests).**
   ```bash
   php artisan test --testsuite=Unit
   ```
   Se ejercitan directamente los métodos y accesores del modelo `Pedido` para asegurar la lógica interna.

3. **Ejecutar pruebas individuales (opcional).**
   ```bash
   php artisan test --filter=PedidoApiTest::test_can_list_pedidos_with_totals
   ```
   Útil para depurar o capturar evidencia de un caso concreto.

> Consejo: si prefieres usar PHPUnit directamente, reemplaza `php artisan test` por `./vendor/bin/phpunit` en los comandos anteriores.

## 3. Capturar evidencia de la ejecución

1. **Guardar la salida en un archivo.**
   ```bash
   php artisan test --testsuite=Feature | tee storage/logs/tests-feature.log
   php artisan test --testsuite=Unit | tee storage/logs/tests-unit.log
   ```
   Conserva los archivos `storage/logs/tests-*.log` como respaldo o adjúntalos en tu entrega.

2. **Generar reportes en formato JUnit (opcional).**
   ```bash
   ./vendor/bin/phpunit --testsuite=Feature --log-junit storage/logs/feature-junit.xml
   ./vendor/bin/phpunit --testsuite=Unit --log-junit storage/logs/unit-junit.xml
   ```
   Los archivos XML son compatibles con herramientas de CI/CD o visores de reportes.

3. **Capturar pantallazos.**
   - Asegúrate de que en la terminal se vea la fecha, el comando ejecutado y el resumen final de PHPUnit (`Tests: X passed`).
   - Puedes usar utilidades como `script` (en Linux/macOS) para grabar la sesión completa: `script storage/logs/pruebas.log`.

4. **Documentar metadatos.**
   Incluye en tu reporte el commit, rama y entorno (por ejemplo, “PHP 8.2, SQLite en memoria”). Esto facilita reproducir los resultados.

## 4. Entregar la evidencia

1. Adjunta los archivos de log o XML generados.
2. Inserta capturas de pantalla en tu informe o presentación.
3. Describe brevemente qué valida cada suite:
   - **Feature:** flujos completos de la API de pedidos (caja negra).
   - **Unit:** cálculos y formatos dentro del modelo `Pedido` (caja blanca).
4. Indica cualquier prueba manual adicional (por ejemplo, verificaciones en Postman) y lo que se comprobó.

Siguiendo estos pasos tendrás un registro claro, reproducible y verificable de las pruebas automatizadas del proyecto.
