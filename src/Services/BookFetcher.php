<?php

namespace App\Services;

// Servicio para manejar la comunicación con la API de Open Library
class BookFetcher {
    /**
     * Realiza una solicitud cURL a una URL dada y devuelve el resultado como un array asociativo.
     * Si la respuesta no es exitosa (código HTTP diferente de 200), devuelve null.
     *
     * @param string $url La URL a la que se realizará la solicitud.
     * @return array|null El resultado decodificado en formato JSON o null si ocurre un error.
     */
    public static function fetchJson(string $url): ?array {
        // Inicializa una sesión cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
         // Ejecuta la solicitud y obtiene la respuesta
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Cierra la sesión cURL
        curl_close($ch);
        // Decodifica y devuelve la respuesta si el código HTTP es 200 (OK)
        return ($http_code === 200) ? json_decode($response, true) : null;
    }

    /**
     * Realiza múltiples solicitudes cURL en paralelo para mejorar el rendimiento (de lo contrario, demoraría mucho en procesar el get y cargar los datos en el front).
     * Devuelve un array de resultados para cada URL proporcionada.
     *
     * @param array $urls Un array de URLs a las que se realizarán las solicitudes.
     * @return array Un array asociativo con las respuestas decodificadas para cada URL.
     */
    public static function fetchMultiple(array $urls): array {
        // Inicializa las sesiones cURL y el manejador múltiple
        $multiCurl = [];
        $results = [];
        $mh = curl_multi_init();
        // Configura cada solicitud cURL y las agrega al manejador múltiple
        foreach ($urls as $key => $url) {
            $multiCurl[$key] = curl_init();
            curl_setopt($multiCurl[$key], CURLOPT_URL, $url);
            curl_setopt($multiCurl[$key], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($multiCurl[$key], CURLOPT_TIMEOUT, 15);
            curl_setopt($multiCurl[$key], CURLOPT_FOLLOWLOCATION, true);
            curl_multi_add_handle($mh, $multiCurl[$key]);
        }

        // Ejecutar las peticiones en paralelo
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        // Recoger resultados de cada solicitud
        foreach ($multiCurl as $key => $ch) {
            $response = curl_multi_getcontent($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // Solo guarda respuestas con código 200 (OK)
            if ($http_code === 200) {
                $results[$key] = json_decode($response, true);
            }
            // Elimina el manejador individual de cURL y cierra la sesión
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        // Cierra el manejador múltiple
        curl_multi_close($mh);

        return $results;
    }
}
