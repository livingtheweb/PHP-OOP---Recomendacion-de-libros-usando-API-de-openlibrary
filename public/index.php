<?php
// Carga automática de las clases usando Composer
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Author;
use App\Services\CsvExporter;

// Leer configuración desde el archivo .env o usar valores predeterminados
$authorLimit = (int) ($_ENV['AUTHOR_LIMIT'] ?? 50);
$totalBookLimit = (int) ($_ENV['TOTAL_BOOK_LIMIT'] ?? 150);
ini_set('max_execution_time', $_ENV['MAX_EXECUTION_TIME'] ?? 300);
ini_set('memory_limit', $_ENV['MEMORY_LIMIT'] ?? '512M');
// Rutas de los archivos
$outputFile = __DIR__ . '/../books_output.csv';
$authorsFile = __DIR__ . '/../authors.csv';

// Verificar que el archivo de autores exista y sea legible
if (!file_exists($authorsFile) || !is_readable($authorsFile)) {
    die('<p>Error: El archivo de autores no se pudo leer o no existe.</p>');
}
// Leer IDs de autores del archivo
$authorIds = array_map('trim', file($authorsFile));
$books = [];
$csvExporter = new CsvExporter($outputFile);

// Configurar encabezado para la salida HTML (además agregué algunos estilos básicos)
header('Content-Type: text/html; charset=utf-8');
echo "<table><tr><th>Author</th><th>Book Title</th><th>Published</th><th>Rating</th><th>Description</th></tr>";
echo "<style>body { font-family: Arial, sans-serif; } table { width: 100%; border-collapse: collapse; margin-bottom: 20px; } th, td { padding: 10px; border: 1px solid #ddd; text-align: left; } th { background-color: #f4f4f4; }</style>";

// Procesar cada autor y sus libros
foreach ($authorIds as $authorId) {
    // Detener si se alcanzó el límite total de libros
    if (count($books) >= $totalBookLimit) break;
    // Crear instancia de la clase autor
    $author = new Author($authorId, $authorLimit);
    // Obtener y procesar libros del autor
    foreach ($author->getBooks() as $book) {
        // Detener si se alcanzó el límite total de libros
        if (count($books) >= $totalBookLimit) break;
        // Agregar libro a la lista
        $books[] = $book;
        // Exportar libro al archivo CSV
        $csvExporter->export($book);
        // Mostrar libro en la tabla HTML
        echo $book->toHtmlRow();
    }
}
// Cerrar tabla HTML (obvio, pero bueno, nunca está demás)
echo "</table>";

// Cerrar el archivo CSV después de exportar todos los libros
$csvExporter->closeFile();
// Mostrar mensaje de confirmación (sólo a efectos de conformar que todo funciona bien y que se llegó a esta instancia del código)
echo "<p>Archivo $outputFile generado correctamente con un máximo de $totalBookLimit libros en total.</p>";
