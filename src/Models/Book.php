<?php

namespace App\Models;

// Clase que representa un libro con sus propiedades y métodos relacionados
class Book {
    private string $author;
    private string $title;
    private string $publishedDate;
    private float $rating;
    private string $description;

    /**
     * Constructor para inicializar las propiedades del libro.
     *
     * @param string $author Nombre del autor del libro.
     * @param string $title Título del libro.
     * @param string $publishedDate Año de publicación del libro.
     * @param float $rating Valoración promedio del libro.
     * @param string $description Descripción del libro.
     */
    public function __construct(string $author, string $title, string $publishedDate, float $rating, string $description) {
        $this->author = $author;
        $this->title = $title;
        $this->publishedDate = $publishedDate;
        $this->rating = $rating;
        $this->description = $description;
    }

    /**
     * Calcula los años que han pasado desde la publicación del libro.
     *
     * @return int Años desde la publicación.
     */
    public function getYearsOld(): int {
        // Calcula la diferencia entre el año actual y el año de publicación
        return date('Y') - intval($this->publishedDate);
    }

    /**
     * Calcula una puntuación para el libro basada en su antigüedad y valoración.
     *
     * @param float $yearWeight Ponderación para la antigüedad (por defecto 0.25).
     * @param float $ratingWeight Ponderación para la valoración (por defecto 0.75).
     * @return float Puntuación calculada para el libro.
     */
    public function getScore(float $yearWeight = 0.25, float $ratingWeight = 0.75): float {
        // Calcula la puntuación usando la antigüedad y el rating
        return ($this->getYearsOld() * $yearWeight) + ($this->rating * $ratingWeight);
    }

    /**
     * Método para exportar los datos del libro como un array para ser usado en archivos CSV.
     *
     * @return array Datos del libro en formato de array.
     */
    public function toArray(): array {
        // Formatea el rating a dos decimales y devuelve todos los datos del libro
        return [$this->author, $this->title, $this->publishedDate, number_format($this->rating, 2), $this->description];
    }

    /**
     * Método para representar el libro como una fila HTML para ser mostrada en una tabla.
     *
     * @return string Fila HTML con los datos del libro.
     */
    public function toHtmlRow(): string {
        // Genera la fila HTML para mostrar los detalles del libro en una tabla
        return "<tr>
            <td>{$this->author}</td>
            <td>{$this->title}</td>
            <td>{$this->publishedDate}</td>
            <td>" . number_format($this->rating, 2) . "</td>
            <td>{$this->description}</td>
        </tr>";
    }
}
