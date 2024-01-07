<?php

return [

    'api' => [
        'title' => 'WillJobsPro API',
        'description' => 'Red Social Tipo Bolsa de Empleo',
        'version' => '1.0.0',
    ],

    'routes' => [
        'api' => 'api/documentation', // Ruta principal de la documentación Swagger
        'docs' => 'docs', // Ruta para los archivos JSON generados
        'oauth2_callback' => 'api/oauth2-callback', // Ruta para el manejo de OAuth2 (si es necesario)
    ],

    'paths' => [
        'docs' => base_path('resources/views/vendor/l5-swagger'), // Ruta a las vistas del explorador Swagger
    ],

    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false), // Regenerar la documentación en cada solicitud (útil en desarrollo)
    'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false), // Generar un archivo YAML además de JSON
];
