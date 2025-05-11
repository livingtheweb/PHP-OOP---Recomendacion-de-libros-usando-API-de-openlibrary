<?php

namespace App\Models;

use App\Services\BookFetcher;

// Clase que representa a un autor y maneja la obtención de sus libros
class Author {
    private string $id;
    private string $name;
    private array $books = [];
    private int $bookLimit;

    /**
     * Constructor que inicializa el autor con su ID y límite de libros.
     * También obtiene el nombre del autor y carga los libros.
     *
     * @param string $id El ID del autor en Open Library
     * @param int $bookLimit El límite de libros a obtener (por defecto 50)
     */
    public function __construct(string $id, int $bookLimit = 50) {
        $this->id = $id;
        $this->bookLimit = $bookLimit;
        $this->name = $this->fetchName(); // Obtener el nombre del autor
        $this->fetchBooks(); // Cargar los libros del autor
    }

    /**
     * Método privado para obtener el nombre del autor desde la API.
     *
     * @return string El nombre del autor o 'Autor desconocidor' si no se encuentra
     */
    private function fetchName(): string {
        $url = "https://openlibrary.org/authors/{$this->id}.json";
        $data = BookFetcher::fetchJson($url);
        return $data['name'] ?? 'Autor desconocidor';
    }

    /**
     * Método privado para obtener los libros del autor desde la API.
     *
     * Carga los datos básicos de los libros y luego busca las valoraciones en paralelo.
     */
    private function fetchBooks(): void {
        $url = "https://openlibrary.org/authors/{$this->id}/works.json";
        $data = BookFetcher::fetchJson($url);
         // Verificar que la respuesta contenga entradas válidas
        if (!$data || empty($data['entries'])) return;

        $bookUrls = []; // URLs para obtener ratings en paralelo
        foreach ($data['entries'] as $entry) {
            // Detener si se alcanza el límite de libros por autor
            if (count($this->books) >= $this->bookLimit) break;
            // Obtener los detalles básicos del libro
            $title = $entry['title'] ?? 'Título desconocido';
            $publishedDate = substr($entry['created']['value'], 0, 4) ?? 'Desconocido';
            $description = $entry['description']['value'] ?? ($entry['description'] ?? 'Sin descripción');
            // Ignorar libros con datos incompletos
            if (!$description || $title === 'Título desconocido' || $publishedDate === 'Desconocido') {
                continue;
            }

            // Preparar URL para obtener el rating del libro
            $bookKey = str_replace('/works/', '', $entry['key']);
            $bookUrls[$bookKey] = "https://openlibrary.org/works/{$bookKey}/ratings.json";

            // Crear el objeto Book con rating temporal (0.0)
            $this->books[$bookKey] = new Book($this->name, $title, $publishedDate, 0.0, $description);
        }

        // Obtener valoraciones en paralelo
        $ratings = BookFetcher::fetchMultiple($bookUrls);

        // Asignar ratings
        foreach ($this->books as $bookKey => $book) {
            $rating = $ratings[$bookKey]['summary']['average'] ?? 0.0;
            if ($rating > 0.0) {
                // Crear un nuevo objeto Book con el rating actualizado
                $bookData = $book->toArray();
                $this->books[$bookKey] = new Book($bookData[0], $bookData[1], $bookData[2], $rating, $bookData[4]);
            } else {
                // Eliminar libros con rating 0.00 si es necesario (línea comentada pues de lo contrario devuelve pocos resultados)
                // unset($this->books[$bookKey]);
            }
        }

        // Limitar a los primeros libros con rating válido si es necesario (línea comentada pues de lo contrario devuelve pocos resultados)
        // $this->books = array_slice(array_values($this->books), 0, $this->bookLimit);
    }

      /**
     * Obtener la lista de libros del autor.
     *
     * @return array Lista de objetos Book
     */
    public function getBooks(): array {
        return $this->books;
    }

    /**
     * Obtener el nombre del autor.
     *
     * @return string El nombre del autor
     */
    public function getName(): string {
        return $this->name;
    }
}
