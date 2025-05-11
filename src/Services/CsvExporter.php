<?php

namespace App\Services;

use App\Models\Book;

/**
 * Clase para exportar datos de libros al archivo CSV.
 */
class CsvExporter {
    private string $outputFile; // Ruta del archivo CSV de salida
    private $fileHandle; // Manejador del archivo CSV

    /**
     * Constructor que inicializa el exportador y abre el archivo CSV para escritura.
     *
     * @param string $outputFile La ruta completa del archivo CSV a crear
     */
    public function __construct(string $outputFile) {
        $this->outputFile = $outputFile;
        $this->openFile(); // Abrir el archivo para escritura
    }

    /**
     * Método privado para abrir el archivo CSV y escribir el encabezado.
     *
     * @throws \RuntimeException Si el archivo no se puede abrir para escritura
     */
    private function openFile(): void {
         // Intentar abrir el archivo para escritura
        $this->fileHandle = fopen($this->outputFile, 'w');
        if (!$this->fileHandle) {
            throw new \RuntimeException("Error: No se pudo abrir el archivo para escribir: {$this->outputFile}");
        }

        // Escribir la primera fila con los encabezados del archivo CSV
        fputcsv($this->fileHandle, ['Author', 'Book Title', 'Published', 'Rating', 'Description'], ';');
    }

    /**
     * Exporta un libro al archivo CSV.
     *
     * @param Book $book El objeto Book a exportar
     */
    public function export(Book $book): void {
        // Escribir los datos del libro como una fila en el archivo CSV
        fputcsv($this->fileHandle, $book->toArray(), ';');
    }

    /**
     * Cierra el archivo CSV al finalizar la exportación.
     */
    public function closeFile(): void {
        fclose($this->fileHandle);
    }
}
